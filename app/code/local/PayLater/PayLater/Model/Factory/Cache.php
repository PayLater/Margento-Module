<?php

/**
 * PayLater extension for Magento
 *
 * Long description of this file (if any...)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the PayLater PayLater module to newer versions in the future.
 * If you wish to customize the PayLater PayLater module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @copyright  Copyright (C) 2012 PayLater
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @subpackage Model
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class PayLater_PayLater_Model_Factory_Cache implements PayLater_PayLater_Model_Interface_Cache
{

	private $_instance = NULL;

	public function __construct()
	{
		$this->_setInstance();
	}

	protected function _getStoreId()
	{
		return Mage::helper('paylater')->getStoreId();
	}

	protected function _setInstance()
	{
		$this->_instance = Zend_Cache::factory('Core', 'File', $this->getFrontendOptions(), $this->getBackendOptions());
	}

	/**
	 *
	 * @return Zend_Cache_Core 
	 */
	public function getInstance()
	{
		return $this->_instance;
	}

	/**
	 *
	 * @return array 
	 */
	public function getFrontendOptions()
	{
		return array(
			'lifetime' => self::FRONTEND_TTL,
			'automatic_serialization' => self::FRONTEND_AUTO_SERIALIZE,
		);
	}

	/**
	 *
	 * @return array 
	 */
	public function getBackendOptions()
	{
		return array(
			'cache_dir' => Mage::getBaseDir('cache'),
			'file_name_prefix' => self::BACKEND_FILE_PREFIX,
			'hashed_directory_level' => 1,
			'hashed_directory_umask' => 0777,
		);
	}

	/**
	 *
	 * @return int 
	 */
	public function getId()
	{
		return sprintf(self::CID_FORMAT, $this->_getStoreId());
	}

}
