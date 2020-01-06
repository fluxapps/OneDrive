<?php
require_once('class.exodItem.php');

/**
 * Class exodFolder
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodFile extends exodItem {

	/**
	 * @var array
	 */
	protected static $ms_formats = array(
		'doc',
		'docx',
		'dot',
		'dotx',
		'xls',
		'xlsx',
		'xlt',
		'xltx',
		'ppt',
		'pptx',

	);
	/**
	 * @var int
	 */
	protected $type = self::TYPE_FILE;
	/**
	 * @var int
	 */
	protected $size = 0;
	/**
	 * @var string
	 */
	protected $content_url = '';


    /**
     * @param string $new_title
     * @param string $old_path An old title or old path. Make sure a file extension is available.
     *
     * @return string
     */
	public static function formatRename($new_title, $old_path) {
        $finalFileName = $new_title;
        $dotAmount = substr_count($new_title, ".");

        if ($dotAmount == 0) {
            $path_parts = pathinfo($old_path);
            $extension = $path_parts['extension'];
            $finalFileName .= "." . $extension;
        }

        return $finalFileName;
    }


	/**
	 * @return bool
	 */
	public function isMsFormat() {
		return in_array($this->getSuffix(), self::$ms_formats);
	}


	/**
	 * @return null
	 */
	public function getMsURL() {
		if (!$this->isMsFormat() || is_null($this->getWebUrl())) {
			return null;
		}

		return $this->getWebUrl();
	}


	/**
	 * @return mixed
	 */
	public function getSuffix() {
		return pathinfo($this->getName(), PATHINFO_EXTENSION);
	}


	/**
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}


	/**
	 * @param int $size
	 */
	public function setSize($size) {
		$this->size = $size;
	}


	/**
	 * @return string
	 */
	public function getContentUrl() {
		return $this->content_url;
	}


	/**
	 * @param string $content_url
	 */
	public function setContentUrl($content_url) {
		$this->content_url = $content_url;
	}
}

