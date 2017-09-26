<?php
namespace Core;

class Theme
{
	function theme($template, $data) 
	{
		ob_start();
		extract($data);
		include_once(TEMPLATE_ROOT . '/' . $template . '.tpl.php');
		$string = ob_get_contents();
		ob_end_clean();
		return $string;
	}
}