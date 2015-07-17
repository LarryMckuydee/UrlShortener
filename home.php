<?php
	session_start();
?>
<html>
	<head>

	</head>
	<body>
		<h2> Test Generate Shortener URL</h2>
		<?php
			if(isset($_SESSION['msg'])){
				echo "<div>".$_SESSION['msg']."</div>";
				unset($_SESSION['msg']);

			}
		?>
		<form method="post" action="generate.php">
			<input type="text" name="url">
			<input type="submit" >
		</form>
	</body>
</html>