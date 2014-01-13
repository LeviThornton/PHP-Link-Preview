<?php
	require_once("LinkPreview.php");
 	$linkPreview = new LinkPreview("https://www.google.com/search?q=lolcats&source=lnms&tbm=isch");
 	var_dump($linkPreview->results());
