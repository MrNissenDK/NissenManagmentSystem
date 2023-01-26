<?php
namespace server\controllers\user;
class HomeController
{
	public function Index()
	{
		return "Hello";
	}
	public function Index_POST()
	{
		return "This is Post";
	}
}