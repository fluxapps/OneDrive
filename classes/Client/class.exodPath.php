<?php

require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/class.ilOneDrive.php');

/**
 * Class exodPath
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodPath {

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
	 * @var string
	 */
	protected $parent_dirname = '';
	/**
	 * @var array
	 */
	protected static $preserved_chars = array(
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
		'\\'
	);


	/**
	 * @param $basename
	 *
	 * @return string
	 * @throws ilCloudException
	 */
    public static function validateBasename($basename)
    {
        global $DIC;
        if (strpbrk($basename, implode('', self::$preserved_chars))) {
            $error_msg = $DIC->language()->txt(ilOneDrivePlugin::getInstance()->getPrefix() . "_err_unsupported_chars");
            throw new ilCloudException(ilCloudException::FOLDER_CREATION_FAILED, $error_msg
                . htmlentities(implode(' ', self::$preserved_chars)));
        }

        return ltrim($basename);
    }


	/**
	 * @param $path
	 *
	 * @return exodPath
	 * @throws ilCloudException
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
		return rawurlencode($path);
	}


	/**
	 * @param $path
	 *
	 * @param $root_path
	 *
	 * @throws ilCloudException
	 */
	protected function __construct($path) {;
		//		$path = '/ILIASCloud/' . ltrim($path, '/');
		$this->path = $path;
		//		$path = ltrim($path, '/');

		$this->initDirname();
		$this->initBasename();
		$this->full_path = $this->encode($this->path);
	}


	/**
	 * @return array
	 */
	public function getParts() {
		return explode('/', ltrim($this->path, "/"));
	}


	/**
	 * @param $nr
	 *
	 * @return string
	 */
	public function getPathToPart($nr) {
		$parts = $this->getParts();
		$path = '';
		for ($x = 0; $x < count($parts) AND $x <= $nr; $x ++) {
			$path .= '/' . $parts[$x];
		}

		return $path;
	}


	/**
	 * @param $nr
	 *
	 * @return string
	 */
	public function getPathToPartEncoded($nr) {
		return $this->encode($this->getPathToPart($nr));
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


	/**
	 * @return string
	 */
	public function getParentDirname() {
		return $this->parent_dirname;
	}


	protected function initDirname() {
		$dirname = dirname($this->path);

		if ($dirname == '/' AND $this->path != '/') {
			$dirname = $this->path;
		}

		if ($dirname == '.') {
			$dirname = '/';
		}

		$this->parent_dirname = $this->encode($this->getPathToPart(count($this->getParts()) - 2));
		if (!$this->parent_dirname) {
			$this->parent_dirname = '/';
		}

		$this->dirname = $this->encode($dirname);
	}


	protected function initBasename() {
	    // basename() replacement, as basename doesn't properly work with umlauts at the beginning
		$pathContents = explode("/", $this->path);
		$basename = $pathContents[count($pathContents) - 1];

		$this->basename = $this->encode(addslashes($basename));
	}
}

