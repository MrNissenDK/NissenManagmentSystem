<?php
namespace server;
class LoadPage
{
	public function __construct() {
		ob_start();
		$load = PathMap::getMap($_SERVER['REQUEST_URI'] ?? '/');
		$load->load();
		$debug = ob_get_clean();
		echo $debug;
	}
}