<?php
/**
 * Link Image Preview
 *
 * @author Levi Thornton
 * <code>
   <?php
	require_once("LinkPreview.php");
 	$linkPreview = new LinkPreview("https://www.google.com/search?q=lolcats&source=lnms&tbm=isch");
 	var_dump($linkPreview->results());
   ?>
 * </code>
 */
class LinkPreview {

	const	X_QUERY1 = "//img[@width > 70 or substring-before(@width, 'px') > 70 or @height > 70 or substring-before(@height, 'px') > 70]";
	const	X_QUERY2 = "//img[(not(@width) and not(@height))]";
	const	X_QUERY3 = "//img";

	private $link;
	private $thumbs;

	public function __construct($addy) {
		$this->link = $addy;
		$thumbs = false;

		try {
			$html = file_get_contents($this->link);
		} catch (Exception $e) {
			print "Caught exception when attempting to find images: ";
			print $e->getMessage(). "\n";
		}

		$dom = new DOMDocument();
			@$dom->loadHTML($html);
		$x = new DOMXPath($dom);

		/**
		* Find images based on width and height
		*/
		foreach($x->query(self::X_QUERY1) as $node)
		{
		    $imgAddy = $node->getAttribute("src");
		    $thumbs[] = $this->rel2abs($imgAddy);
		}

		/**
		* If all else fails to find images without a width or height
		*/
		if($thumbs==false) {
			foreach($x->query(self::X_QUERY2) as $node)
			{
				$imgAddy = $node->getAttribute("src");
				$thumbs[] = $this->rel2abs($imgAddy);
			}
		}
		/**
		* If no matching at all.. time for an intervention
		*/
		if($thumbs==false) {
			foreach($x->query(self::X_QUERY3) as $node)
			{
				$url = $node->getAttribute("src");
				$thumbs[] = ""; // return a default no image
			}
		}
		// return results
		$this->thumbs = $thumbs;
	}

	public function results() {
		return $this->thumbs;
	}

	private function rel2abs($url) {
		if (substr($url, 0, 4) == 'http') {
			return $url;
		} else {
			$hparts = explode('/', $this->link);
			if ($url[0] == '/') {
				return implode('/', array_slice($hparts, 0, 3)) . $url;
			} else if ($url[0] != '.') {
				array_pop($hparts);
				return implode('/', $hparts) . '/' . $url;
			}
		}
	}
}
?>