<?php

/**
 * Class exodItem
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
abstract class exodItem {

	const TYPE_UNKNOWN = - 1;
	const TYPE_FOLDER = 1;
	const TYPE_FILE = 2;
	/**
	 * @var string
	 */
	protected $id = '';
	/**
	 * @var string
	 */
	protected $path = '';
	/**
	 * @var string
	 */
	protected $parent_id = '';
	/**
	 * @var int
	 */
	protected $type = self::TYPE_UNKNOWN;
	/**
	 * @var string
	 */
	protected $web_url = '';
	/**
	 * @var string
	 */
	protected $date_time_created = '';
	/**
	 * @var string
	 */
	protected $date_time_last_modified = '';
	/**
	 * @var string
	 */
	protected $name = '';
	/**
	 * @var string
	 */
	protected $last_modified_by = '';
	/**
	 * @var string
	 */
	protected $created_by = '';
	/**
	 * @var string
	 */
	protected $e_tag = '';


	/**
	 * @param stdClass $item
	 */
	public function loadFromStdClass(stdClass $item) {
		foreach ($item as $k => $v) {
			$internal_key = self::fromCamelCase($k);
			if (property_exists(get_class($this), $internal_key)) {
				$this->{$internal_key} = $v;
			}
			if ($k == 'parentReference') {
				$this->setPath($v->path);
			}
		}
	}


	/**
	 * @return string
	 */
	public function getFullPath() {
		$path = '';
		if ($this->getPath() AND $this->getPath() != '/') {
			$path = $this->getPath();
		}

		return $path . '/' . $this->getName();
	}


	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}


	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}


	/**
	 * @return string
	 */
	public function getParentId() {
		return $this->parent_id;
	}


	/**
	 * @param string $parent_id
	 */
	public function setParentId($parent_id) {
		$this->parent_id = $parent_id;
	}


	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param int $type
	 */
	public function setType($type) {
		$this->type = $type;
	}


	/**
	 * @return string
	 */
	public function getWebUrl() {
		return $this->web_url;
	}


	/**
	 * @param string $web_url
	 */
	public function setWebUrl($web_url) {
		$this->web_url = $web_url;
	}


	/**
	 * @return string
	 */
	public function getDateTimeCreated() {
		return $this->date_time_created;
	}


	/**
	 * @param string $date_time_created
	 */
	public function setDateTimeCreated($date_time_created) {
		$this->date_time_created = $date_time_created;
	}


	/**
	 * @return string
	 */
	public function getDateTimeLastModified() {
		return $this->date_time_last_modified;
	}


	/**
	 * @param string $date_time_last_modified
	 */
	public function setDateTimeLastModified($date_time_last_modified) {
		$this->date_time_last_modified = $date_time_last_modified;
	}


	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * @return string
	 */
	public function getLastModifiedBy() {
		return $this->last_modified_by;
	}


	/**
	 * @param string $last_modified_by
	 */
	public function setLastModifiedBy($last_modified_by) {
		$this->last_modified_by = $last_modified_by;
	}


	/**
	 * @return string
	 */
	public function getCreatedBy() {
		return $this->created_by;
	}


	/**
	 * @param string $created_by
	 */
	public function setCreatedBy($created_by) {
		$this->created_by = $created_by;
	}


	/**
	 * @return string
	 */
	public function getETag() {
		return $this->e_tag;
	}


	/**
	 * @param string $e_tag
	 */
	public function setETag($e_tag) {
		$this->e_tag = $e_tag;
	}


	/**
	 * @param      $str
	 * @param bool $capitalise_first_char
	 *
	 * @return string
	 */
	public static function toCamelCase($str, $capitalise_first_char = false) {
		if ($capitalise_first_char) {
			$str[0] = strtoupper($str[0]);
		}
		$func = create_function('$c', 'return strtoupper($c[1]);');

		return preg_replace_callback('/_([a-z])/', $func, $str);
	}


	/**
	 * @param string $str
	 *
	 * @return string
	 */
	protected static function fromCamelCase($str) {
		$str[0] = strtolower($str[0]);
		$func = create_function('$c', 'return "_" . strtolower($c[1]);');

		return preg_replace_callback('/([A-Z])/', $func, $str);
	}
}

?>
