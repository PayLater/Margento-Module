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
class PayLater_PayLater_Model_Cache_Factory implements PayLater_PayLater_Core_Interface, PayLater_PayLater_Cache_Interface
{

	private $_instance = NULL;

	protected function _getStoreId()
	{
		return Mage::helper('paylater')->getStoreId();
	}

	protected function _setInstance()
	{
		$this->_instance = Zend_Cache::factory('Core', 'File', $this->getFrontendOptions(), $this->getBackendOptions());
	}
	
	protected function _validate ($data)
	{
		return is_array($data) && array_key_exists(self::FEE_PERCENT_KEY, $data) && array_key_exists(self::ORDER_LOWER_BOUND, $data) && array_key_exists(self::ORDER_UPPER_BOUND, $data);
	}
	
	protected function _savePayLaterRange($orderLowerBound, $orderUpperBound)
	{
		$config = Mage::helper('paylater')->getCoreConfig();
		$config->saveConfig('payment/paylater/min_order_total', $orderLowerBound, 'default', 0);
		$config->saveConfig('payment/paylater/max_order_total', $orderUpperBound, 'default', 0);
	}
	
	public function __construct()
	{
		$this->_setInstance();
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
	/**
	 * Returns TRUE if PayLater cache has expired or cannot be loaded,
	 * FALSE otherwise.
	 * 
	 * @return bool 
	 */
	public function hasExpired()
	{
		return $this->getInstance()->load($this->getId()) ? FALSE : TRUE;
	}
	/**
	 *
	 * Saves PayLater data if service is available, it is possible to retrieve config
	 * for merchant and cache is expired.
	 * 
	 * Returns data written or loaded from cache.
	 * 
	 * @param PayLater_PayLater_Cache_Interface $cacheFactory 
	 * @return array
	 * @throws PayLater_PayLater_Exception_InvalidMerchantData
	 * @throws PayLater_PayLater_Exception_ServiceUnavailable
	 */
	public function save ()
	{
		if (Mage::helper('paylater')->isServiceAvailable()) {
			if ($this->hasExpired()) {
				$merchantCdn = Mage::helper('paylater')->getMerchantServiceCdn();
				$client = new Zend_Http_Client($merchantCdn);
				$data = json_decode($client->request()->getBody(), true);
				if (!($this->_validate($data))) {
					throw new PayLater_PayLater_Exception_InvalidMerchantData(Mage::helper('paylater')->__('Invalid Merchant Data'));
				}
				$lowerBound = $data[self::ORDER_LOWER_BOUND];
				$upperBound = $data[self::ORDER_UPPER_BOUND];
				$this->getInstance()->save($data);
				$this->_savePayLaterRange($lowerBound, $upperBound);
			}
			return $this->getInstance()->load($this->getId());
		}
		throw new PayLater_PayLater_Exception_ServiceUnavailable(Mage::helper('paylater')->__('Service unavailable'));
	}
}
