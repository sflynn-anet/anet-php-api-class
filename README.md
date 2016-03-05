Atlantic.Net Cloud API - PHP Class
======

### Example Code

```
require_once("anetcloud.class.php");

define('APIKEY', '<%API-KEY%>');
define('PKEY', '<%API-PRIVATE-KEY%>');

$anet = new AnetCloud(APIKEY, PKEY);

/** LIST INSTANCES EXAMPLE **/
$instances = $anet->get_instances();
print_r($instances);

/** LIST IMAGES EXAMPLE **/
$images = $anet->get_images();
$imageList = isset($images['describe-imageresponse']['imagesset']) ? $images['describe-imageresponse']['imagesset'] : false;
# print_r($images);

if($imageList !== false) {
	foreach($imageList as $img) {
		
		$image_id			= $img['imageid'];
		$image_name			= $img['displayname'];
		$image_platform		= $img['platform'];
		$image_architecture	= $img['architecture'];
		$image_ostype		= $img['ostype'];
		$image_type			= $img['image_type'];
		$image_version 		= $img['version'];
		
		
		echo "[$image_id] $image_name" . PHP_EOL;
		
	}	
}


```