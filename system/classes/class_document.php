<?php


class document{
	
	public static function _addScript( $src, $rel=true, $basedir= "includes/js/") {
		
		if (!is_array($src) ) $src = array($src);
		$return = array();
		foreach($src as $s) {
			if ($rel && strpos($s,"http") === false) {
				$return[] .= <<<HTML
<script src="{$basedir}{$s}" language="javascript" type="text/javascript"></script>
HTML;
				}
			else {
				$return[] .= <<<HTML
<script src="{$s}" language="javascript" type="text/javascript"></script>
HTML;
			}
		}
		return implode("\n",$return);
	}
	
	public static function _addStyle( $src, $rel=true, $basedir= "includes/css/", $media="screen") {
		$rel = ($rel == "default" || $rel === NULL) ? true : $rel;
		$basedir = ($basedir == "default" || $basedir === NULL) ? "includes/css/" : $basedir;
		
		if (!is_array($src) ) $src = array($src);
		$return = array();
		foreach($src as $s) {
			if ($rel && strpos($s,"http") === false) {
				$return[] .= <<<HTML
<link rel="stylesheet" href="{$basedir}{$s}" media="{$media}" />
HTML;
				}
			else {
				$return[] .= <<<HTML
<link rel="stylesheet" href="{$s}" media="{$media}" />
HTML;
			}
		}
		return implode("\n",$return);
	}

	public static function _test() {
		echo "hello world";
	}
}

?>