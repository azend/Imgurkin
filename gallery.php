<?php
session_start();

$status = '';

function rebuild_cache () {
	global $status;
	$status = $status . "Rebuilding cache.";
	$_SESSION['gallery'] = json_decode( file_get_contents('http://imgur.com/gallery.json'), true );
	$_SESSION['gallery'] = array_reverse( $_SESSION['gallery']['gallery'] );
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


$action = stripslashes($_GET['action']);

if ($action == "next") {
	$status = $status . "Next.";
	if ($_SESSION['gallery_pointer'] === count($_SESSION['gallery']) - 1) {
		$_SESSION['gallery_pointer'] = 0;
	}
	else {
		$_SESSION['gallery_pointer'] ++;
	}
}
else if ($action == "prev") {
	$status = $status . "Prev.";
	if ($_SESSION['gallery_pointer'] === 0) {
		$_SESSION['gallery_pointer'] = count($_SESSION['gallery']) - 1;
	}
	else {
		$_SESSION['gallery_pointer'] --;
	}
}
else if ($action == "rebuild") {
	$status = $status . "Rebuild.";
	rebuild_cache();
}

// Set up easy variables for rendering
$image = $_SESSION['gallery'][$_SESSION['gallery_pointer']];
$title = $image['title'];
$image_url  = "http://imgur.com/" . $image['hash'] . $image['ext'];

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<h1 id="title"><?php echo $title; ?></h1>
		<a id="prev" class="nav" href="?action=prev">Prev</a>
		<a id="next" class="nav" href="?action=next">Next</a>
		<img id="image" src="<?php echo $image_url; ?>" >
		<a id="rebuild" class="nav" href="?action=rebuild">Rebuild Cache</a>
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