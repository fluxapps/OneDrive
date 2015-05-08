<?php

/**
 * Class exodPathPublic
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodPathPublic {

	/**
	 * @var string
	 */
	protected $path = '';
	/**
	 * @var string
	 */
	protected $basename = '';
	/**
	 * @var string
	 */
	protected $dirname = '';
	/**
	 * @var string
	 */
	protected $full_path = '';
	/**
	 * @var array
	 */
	protected static $preserved_chars = array(
		"'",
		'"',
		'|',
		'#',
		'%',
		'*',
		':',
		'<',
		'>',
		'?',
		'/',
	);


	/**
	 * @param $path
	 *
	 * @return exodPath
	 */
	public static function getInstance($path) {
		return new self($path);
	}


	/**
	 * @param $path
	 *
	 * @return string
	 */
	protected function encode($path) {
		return $path;
		return rawurlencode($path);
	}


	protected function __construct($path) {
//		$path = ltrim($path, '/');
		$this->path = $path;

		$this->initDirname();
		$this->initBasename();
		$this->full_path = $this->encode($this->path);
	}


	/**
	 * @return string
	 */
	public function getBasename() {
		return $this->basename;
	}


	/**
	 * @return string
	 */
	public function getDirname() {
		return $this->dirname;
	}


	/**
	 * @return string
	 */
	public function getFullPath() {
		return $this->full_path;
	}


	protected function initDirname() {
		$dirname = dirname($this->path);
//				throw new ilCloudException(- 1, $dirname . '|||' . $this->path);
		if ($dirname == '.' AND $this->path != '.') {
			$dirname = $this->path;
		}
		if ($dirname == '.') {
			$dirname = '';
		}
		$this->dirname = $this->encode($dirname);
	}


	protected function initBasename() {
		$basename = basename($this->path);
		if (strpbrk($basename, implode('', self::$preserved_chars))) {
			throw new ilCloudException(ilCloudException::FOLDER_CREATION_FAILED, '<b>Name contains unsupported Characters: </b>'
				. htmlentities(implode(' ', self::$preserved_chars)));
		}
		$this->basename = $this->encode(addslashes($basename));
	}
}

?>
