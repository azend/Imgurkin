<?php
session_start();

$status = '';

function rebuild_cache () {
	global $status;
	$status = $status . "<h1>Rebuilding cache.</h1>";

	// Grab a copy of the gallery from imgur
	$galleryResponseRaw = file_get_contents('http://imgur.com/gallery.json');
	$galleryResponse = json_decode( $galleryResponseRaw, true );

	$gallery = $galleryResponse['data'];

	$_SESSION['gallery'] = $gallery;
	$_SESSION['gallery_pointer'] = 0;
}

// Check if cache is downloaded yet
if (isset($_SESSION['gallery']) && !empty($_SESSION['gallery'])) {
	// Cache has been grabbed
}
else {
	// Go get the cache
	rebuild_cache();
}


if ( isset($_GET['action']) && !empty($_GET['action']) ) {
	$action = stripslashes($_GET['action']);

	if ($action == "next") {
		$status = $status . "Next.";
		if ($_SESSION['gallery_pointer'] === count($_SESSION['gallery']) - 1) {
			$_SESSION['gallery_pointer'] = 0;
		}
		else {
			$_SESSION['gallery_pointer'] ++;
		}
		header('Location: ' . strtok( $_SERVER['REQUEST_URI'], '?' ) , TRUE, 307);
	}
	else if ($action == "prev") {
		$status = $status . "Prev.";
		if ($_SESSION['gallery_pointer'] === 0) {
			$_SESSION['gallery_pointer'] = count($_SESSION['gallery']) - 1;
		}
		else {
			$_SESSION['gallery_pointer'] --;
		}
		header('Location: ' . strtok( $_SERVER['REQUEST_URI'], '?' ) , TRUE, 307);
	}
	else if ($action == "rebuild") {
		$status = $status . "Rebuild.";
		rebuild_cache();
	}
}
else {

	// Set up easy variables for rendering
	$image = $_SESSION['gallery'][$_SESSION['gallery_pointer']];
	$title = $image['title'];
	$image_url  = "http://imgur.com/" . $image['hash'] . $image['ext'];

	?>

	<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<link rel="stylesheet" href="css/normalize.css">
			<link rel="stylesheet" href="css/style.css">
		</head>
		<body>
			<div id="header" class="header">
				<h1 id="title" class="headerItem"><?php echo $title; ?></h1>
				<div id="nav" class="headerItem">
					<a id="prev" class="nav" href="?action=prev">Prev</a>
					<a id="next" class="nav" href="?action=next">Next</a>
				</div>
			</div>
			<img id="image" src="<?php echo $image_url; ?>" >
			<div class="footer">
				<a id="rebuild" class="nav" href="?action=rebuild">Rebuild Cache</a>
			</div>
		</body>
		<script type="text/javascript">
			(function() {
				function handleKeypress (e) {

					// Next
					if (e.keyCode === 110) {
						console.log('next');
						window.location.href = document.getElementById('next').href;
					}
					// Prev
					else if (e.keyCode === 112) {
						window.location.href = document.getElementById('prev').href;
					}
				}

				document.onkeypress = handleKeypress;
				document.getElementById('next').focus();
			})();
		</script>
	</html>

	<?php

}