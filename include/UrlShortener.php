<?php
require_once('Databases.php');
class UrlShortener {
	var $conn;
	var $errormsg;
	var $shortenedURL;
	
	public function __construct(){
		
		if(!$this->connect()){
			throw new Exception('Could not connect to database');

		}
		return true;

	}

	private function connect(){
		$this->conn = new mysqli(MYSQL_IP,MYSQL_USERNAME,MYSQL_PASSWORD,MYSQL_DATABASE);
		return $this->conn;
	}

	public function shortenUrl($url){
		$url = trim($url);
		if(!filter_var($url,FILTER_VALIDATE_URL)){
			$this->HandleError("Please enter valid url");
			return false;
		}
		if($this->shortenedURL = $this->urlExist($url)){
			return $this->shortenedURL;
		}
		if($this->shortenedURL = $this->saveURL($url)){
			return $this->shortenedURL;
		}else{
			return false;
		}
	}
	private function generateCode($id){
		return base_convert($id, 10, 36);
	}

	function urlExist($url){
		$stmt = $this->conn->prepare("SELECT code FROM urls WHERE url=?");
		$stmt->bind_param("s",$url);
		$stmt->execute();
		$result = $stmt->get_result();
		if(($result->num_rows)>0){
			$row = $result->fetch_assoc();
			return $row['code'];
		}
		return false;
	}

	private function saveURL($url){


		if($stmt = $this->conn->prepare("INSERT INTO urls (url) VALUES (?)")){
			$stmt->bind_param("s",$url);
			if(!$stmt->execute()){
				$this->HandleError("Some Error happened while try to save url:".$this->conn->error);
				return false;
			}
		}else{
			$this->HandleError("Failed to prepare for insert url");
			return false;
		}

		if($stmt = $this->conn->prepare("SELECT id FROM urls WHERE url=?")){
			$stmt->bind_param("s",$url);
			$stmt->execute();
			$result = $stmt->get_result();
			if(($result->num_rows)>0){
				$row = $result->fetch_assoc();
				
			}else{
				$this->HandleError("Failed to retrieve result from save url, must be no data insert");
				return false;
			}

		}else{
			$this->HandleError("Failed to prepare for select url");
			return false;
		}
		$generateCode = $this->generateCode($row['id']);
		if($stmt = $this->conn->prepare("UPDATE urls SET code = ? WHERE url = ?")){
			$stmt->bind_param("ss",$generateCode,$url);
			if($stmt->execute()){
				if($stmt->affected_rows==0){
					$this->HandleError("Failed to update url");
					return false;
				}
			}else{
				$this->HandleError("Failed to execute update url query");
				return false;
			}
		}else{
			$this->HandleError("Failed to prepare for update url");
			return false;
		}

		return $generateCode;
	}

	function HandleError($error){
		$this->errormsg.=$error."\r\n";
	}

	function getErrorMsg(){
		if(empty($this->errormsg)){
			return '';
		}
		$error=nl2br(htmlentities($this->errormsg));
		return $error;
	}

	function fetchURL($code){
		if($stmt = $this->conn->prepare("SELECT url FROM urls WHERE code=?")){
			$stmt->bind_param("s",$code);
			$stmt->execute();
			$result = $stmt->get_result();
			if(($result->num_rows)>0){
				$row = $result->fetch_assoc();
				return $row['url'];
			}else{
				$this->HandleError("Failed to retrieve result from shortened url");
				return false;
			}

		}else{
			$this->HandleError("Failed to prepare for shortened url");
			return false;
		}
	}


}
?>