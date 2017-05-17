<?php

/**
 * Class exodTenant
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodTenant {

	/**
	 * @var string
	 */
	protected $tenant_id = '';
	/**
	 * @var string
	 */
	protected $tenant_name = '';


	/**
	 * @return string
	 */
	public function getTenantId() {
		return $this->tenant_id;
	}


	/**
	 * @param string $tenant_id
	 */
	public function setTenantId($tenant_id) {
		$this->tenant_id = $tenant_id;
	}


	/**
	 * @return string
	 */
	public function getTenantName() {
		return $this->tenant_name;
	}


	/**
	 * @param string $tenant_name
	 */
	public function setTenantName($tenant_name) {
		$this->tenant_name = $tenant_name;
	}
}
