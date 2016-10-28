<?php
session_start();
?>

	<html>

	<head>
		<meta charset="utf-8" />
		<link href="css/styles.css" rel="stylesheet" type="text/css" />
		<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
		<script src="/js/events.js"></script>
	</head>
	<title>
		RosPuhMeh
	</title>

	<body>
		<header>
			<div id="header-logo">
				ROSPUHMEH TENDERS
			</div>
			<div id='auth-form'>
			<?php 
			if (isset($_SESSION['username'])){
				echo '<div>Здравствуйте, ' . $_SESSION['username'] . '</div>'.
				"<button class='tender-submit-button' onclick='tender.logOut()'>LOGOUT</button>";
			}
			else {
				echo "<input type='text' placeholder='login' id='login-field'>".
			"<input type='password' placeholder='password' id='password-field'>". 
			"<button class='tender-submit-button' onclick='tender.loginCheck()'>LOGIN</button>";
			}
			?>
			</div>
	</header>
	<nav class="tender-nav">
		<section class="tender-control">
			<div id="tenderNameContainer">
				<div><input type="text" name="tenderName" class="tender-input" id="tenderName" placeholder="NAME"></div>
			</div>
			<div id="tenderPriceContainer">
				<div><input type="text" name="tenderPrice" class="tender-input" id="tenderPrice" placeholder="PRICE"></div>
			</div>
			<input type="hidden" name="tenderHash">
			<div>
				<button onclick="tender.addToDB()" class="tender-submit-button">SUBMIT</button>
			</div>
		</section> 
	</nav>
	<h2 id="main-section-header">Tenders available</h2>
	<main id="tender-content">
	</main>
	<footer class="tender-footer">
		<div>
			© Monkey Coding Limited, 2016-2020
		</div>
	</footer>
</body>
</html>