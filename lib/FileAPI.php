<?php
namespace Lib;

class FileAPI extends Base {

	private $file_path;

	/**
	 * Initialize
	 */
	public function __construct($file_path){
		if(!is_dir($file_path))
			mkdir($file_path);
		$this->file_path = $file_path;
	}

	public function createFile($filename, $data){
		$path = $this->file_path . DIRECTORY_SEPARATOR . $filename;
		file_put_contents($path, $data);
		if(file_exists($path)) {
			$DatabaseAPI = new \Lib\DatabaseAPI();
			if($fid = $DatabaseAPI->createFile($filename))
				return $fid;
			else
				return false;			
		}
		return false;
	}	
}