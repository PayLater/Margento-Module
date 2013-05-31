<?php

/**
 * PayLater extension for Magento
 *
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
 * please contact PayLater.
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @copyright  Copyright (C) 2013 PayLater
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @subpackage Model
 * @author     GPMD <dev@gpmd.co.uk>
 */
class PayLater_PayLater_Helper_Data extends Mage_Core_Helper_Data implements PayLater_PayLater_Core_Interface
{

	/**
	 *
	 * @var GPMD_Data_Inflector 
	 */
	protected $_inflector;

	/**
	 * Array of PayLater error codes
	 * See: PayLater Integration Guide - Developer error codes
	 * 
	 * @see PayLater_PayLater_Core_Interface
	 * 
	 * @var array 
	 */
	protected $_errorCodes = array();

	/**
	 * Array of allowed currency 
	 * 
	 * @see PayLater_PayLater_Core_Interface
	 * 
	 * @var array 
	 */
	protected $_allowedCurrencies = array();

	/**
	 *  Gets store config value for node and key passed as argument.
	 * 
	 * @param string $suiteModule
	 * @param string $node
	 * @param string $key
	 * @return mixed 
	 */
	protected function _getModuleConfig($node, $key, $storeCode = false)
	{
		if ($storeCode) {
			return Mage::getStoreConfig($this->getModuleName() . '/' . $node . '/' . $key, $storeCode);
		}
		return Mage::getStoreConfig($this->getModuleName() . '/' . $node . '/' . $key, $this->getStoreId());
	}

	/**
	 * Returns system log status
	 * 
	 * @return int 
	 */
	protected function _getSystemLogStatus()
	{
		return Mage::getStoreConfig(self::XML_NODE_SYSTEM_DEV_LOG_ACTIVE, $this->getStoreId());
	}

	/**
	 * Returns system log status
	 * 
	 * @return int 
	 */
	protected function _getSystemLogActiveStatus()
	{
		return Mage::getStoreConfig(self::XML_NODE_SYSTEM_DEV_LOG_ACTIVE, $this->getStoreId());
	}

	protected function _getPaymentMethod()
	{
		return Mage::getModel('paylater/checkout_onepage')->getPaymentMethod();
	}

	/**
	 * Magic method __call handles methods starting with:
	 * 
	 * getPayLaterConfig[CAMELCASED CONFIG PATH]([CONFIG NODE])
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return mixed 
	 */
	public function __call($name, $arguments)
	{
		if (preg_match('/getPayLaterConfig\w/', $name)) {
			$name = str_replace('getPayLaterConfig', '', $name);
			$inflectedName = $this->getInflector()->underscore($name);
			if (array_key_exists(1, $arguments) && $arguments[1] !== false) {
				return $this->_getModuleConfig($arguments[0], $inflectedName, $arguments[1]);
			}
			return $this->_getModuleConfig($arguments[0], $inflectedName);
		}
	}

	/**
	 * Fills errorCodes and allowed currencies array
	 *  
	 */
	public function __construct()
	{
		foreach (explode(self::ERROR_SEPARATOR, self::ERROR_CODES) as $errorCode) {
			$const = 'ERROR_' . $errorCode;
			$this->_errorCodes[$errorCode] = constant('PayLater_PayLater_Core_Interface::' . $const);
		}

		foreach (explode(self::CURRENCY_SEPARATOR, self::PAYLATER_CURRENCIES) as $currencyCode) {
			$this->_allowedCurrencies[$currencyCode] = $currencyCode;
		}
	}

	public function canLog()
	{
		return $this->_getSystemLogActiveStatus() && $this->getPayLaterConfigLogEnabled('dev');
	}

	/**
	 * Logs message to gpmd_giftwraplite.log file or to other file if
	 * specified.
	 * 
	 * @param string $message
	 */
	public function log($message, $info, $type = Zend_Log::INFO)
	{
		if ($this->canLog()) {
			Mage::log($info . ' ' . $message, $type, $this->getPayLaterConfigLogFile('dev'));
		}
	}

	/**
	 * Returns current currency code
	 * @return string 
	 */
	public function getStoreCurrency()
	{
		return Mage::app()->getStore()->getCurrentCurrencyCode();
	}

	/**
	 * Returns current store currency symbol
	 * @return type 
	 */
	public function getStoreCurrencySymbol()
	{
		return Mage::app()->getLocale()->currency($this->getStoreCurrency())->getSymbol();
	}

	/**
	 * Returns TRUE if store currency is allowed
	 * @return bool 
	 */
	public function isAllowedCurrency($currency = false)
	{
		if ($currency === false) {
			$currency = $this->getStoreCurrency();
		}
		return array_key_exists($currency, $this->_allowedCurrencies);
	}
	
	/**
	 * Returns helper's module name in format modulename
	 * 
	 * @return string 
	 */
	public function getModuleName()
	{
		$name = $this->_getModuleName();
		list($company, $module) = explode('_', $name);
		return strtolower(implode('_', array($module)));
	}

	/**
	 *
	 * @return PayLater_PayLater_Helper_Inflector 
	 */
	public function getInflector()
	{
		if (!isset($this->_inflector)) {
			$this->_inflector = new PayLater_PayLater_Helper_Inflector();
		}
		return $this->_inflector;
	}

	/**
	 * Returns value for config node passed as argument,
	 * or false otherwise.
	 * 
	 * @param string $node
	 * @return mixed 
	 */
	public function getConfigNode($node)
	{
		return Mage::getConfig()->getNode($node);
	}

	/**
	 * Checks whether PayLater service is available.
	 * 
	 * Tries connecting to service host at specified port, and returns resource (stream) if successful,
	 * or false otherwise.
	 * 
	 * @return resource/bool 
	 */
	public function isServiceAvailable($timeout = false)
	{
		if ($timeout === false) {
			$timeout = self::SERVICE_TIMEOUT;
		}
		if ($this->isTestEnvironment()) {
			return fsockopen(self::SERVICE_HOSTNAME_TEST, self::SERVICE_PORT_TEST, $errno, $errstr, $timeout);
		}
		return fsockopen(self::SERVICE_HOSTNAME_LIVE, self::SERVICE_PORT, $errno, $errstr, $timeout);
	}

	/**
	 * Checks whether PayLater endpoint is available.
	 * 
	 * Tries connecting to endpoint host at specified port, and returns resource (stream) if successful,
	 * or false otherwise.
	 * 
	 * @return resource/bool 
	 */
	public function isEndpointAvailable($timeout = false)
	{
		if ($timeout === false) {
			$timeout = self::SERVICE_TIMEOUT;
		}
		if ($this->isTestEnvironment()) {
			return fsockopen(self::PAYLATER_ENDPOINT_TEST_SERVER, self::PAYLATER_ENDPOINT_TEST_SERVER_PORT, $errno, $errstr, $timeout);
		}
		return fsockopen(self::PAYLATER_ENDPOINT_SERVER, self::PAYLATER_ENDPOINT_SERVER_PORT, $errno, $errstr, $timeout);
	}

	/**
	 * Returns TRUE if enviroment is 'test', FALSE otherise (assume 'live') 
	 * 
	 * @return boolean 
	 */
	public function isTestEnvironment()
	{
		$env = $this->getPayLaterConfigEnv('globals');
		return $env == self::ENVIRONMENT_TEST ? TRUE : FALSE;
	}

	/**
	 * Returns TRUE if PayLater cache has expired or cannot be loaded,
	 * FALSE otherwise.
	 * 
	 * @param PayLater_PayLater_Cache_Interface $cacheFactory
	 * @return bool 
	 */
	public function hasCacheExpired(PayLater_PayLater_Cache_Interface $cacheFactory)
	{
		return $cacheFactory->hasExpired();
	}

	/**
	 *
	 * Saves cache data for given cache factory
	 * 
	 * @param PayLater_PayLater_Cache_Interface $cacheFactory 
	 * @return array
	 */
	public function loadCacheData(PayLater_PayLater_Cache_Interface $cacheFactory)
	{
		try {
			$data = $cacheFactory->save();
		} catch (PayLater_PayLater_Exception_InvalidMerchantData $e) {
			$this->log($this->__('Invalid Merchant Data Exception. Cache data load returns false'), __METHOD__);
			$data = false;
		} catch (PayLater_PayLater_Exception_ServiceUnavailable $e) {
			$this->log($this->__('Caught Service Unavailable Exception'), __METHOD__);
			$data = false;
			// try loading data from cache if not expired
			if (!$this->hasCacheExpired($cacheFactory)) {
				$this->log($this->__('Caught Service Unavailable Exception, data loaded from cache'), __METHOD__);
				$data = $cacheFactory->getInstance()->load($cacheFactory->getId());
			}
		} catch (Exception $e) {
			$this->log($this->__('Caught General Exception. Cache data load returns false'), __METHOD__);
			$data = false;
		}
		return $data;
	}

	/**
	 * Gets merchant CDN
	 * @return type 
	 */
	public function getMerchantServiceCdn()
	{
		return sprintf(self::MERCHANTS_CDN, $this->_getModuleConfig('merchant', 'guid'));
	}

	/**
	 * Returns merchant reference set in module config
	 * 
	 * @return string|bool 
	 */
	public function getMerchantReference()
	{
		return $this->_getModuleConfig('merchant', 'reference');
	}

	/**
	 * Returns Magento core config model
	 * 
	 * @return Mage_Core_Config_Data 
	 */
	public function getCoreConfig()
	{
		return Mage::getConfig();
	}

	/**
	 * Returns true if PayLater can show on a particular product and verified the
	 * price is within PayLater range.
	 * 
	 * Returns false otherwise.
	 * 
	 * @return boolean 
	 */
	public function canShowOnProduct()
	{
		$isEnabled = $this->getPayLaterConfigRunStatus('globals');

		if ($isEnabled) {
			$cache = Mage::getModel('paylater/cache_factory');
			$payLaterData = $this->loadCacheData($cache);
			if (is_array($payLaterData)) {
				$currentProduct = Mage::getModel('paylater/catalog_product');
				if ($currentProduct->isWithinPayLaterRange($payLaterData)) {
					return true;
				}
			}
		}

		return false;
	}
	
	/**
	 * Returns true if PayLater can show on a cart product and verified the
	 * price is within PayLater range.
	 * 
	 * Returns false otherwise.
	 * 
	 * @return boolean 
	 */
	public function canShowOnCart()
	{
		return $this->canShowAtCheckout();
	}

	/**
	 * Returns true if PayLater can show at checkout and verified the
	 * price is within PayLater range.
	 * 
	 * Returns false otherwise.
	 * 
	 * @return boolean 
	 */
	public function canShowAtCheckout()
	{
		$isEnabled = $this->getPayLaterConfigRunStatus('globals');

		if ($isEnabled) {
			$cache = Mage::getModel('paylater/cache_factory');
			$payLaterData = $this->loadCacheData($cache);
			if (is_array($payLaterData)) {
				$quote = Mage::getModel('paylater/checkout_quote');
				if ($quote->isWithinPayLaterRange($payLaterData)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Takes an error code as argument and returns message for code,
	 * or FALSE otherwise.
	 * 
	 * @param int $code
	 * @return string|bool 
	 */
	public function getErrorMessageByCode($code)
	{
		return array_key_exists($code, $this->_errorCodes) ? $this->_errorCodes[$code] : FALSE;
	}

	/**
	 * Gets session vars for PayLater offer stored at review checkout step
	 * 
	 * @return array|bool 
	 * @deprecated since changed storage to quote/order table
	 */
	public function getCheckoutOffer()
	{
		$session = Mage::getSingleton(self::PAYLATER_SESSION_MODEL);
		return $session->getData(self::PAYLATER_SESSION_DATA_KEY);
	}

	/**
	 * Unset session data for PayLater offer 
	 * 
	 * @deprecated since changed storage to quote/order table
	 */
	public function unsetCheckoutOffer()
	{
		$session = Mage::getSingleton(self::PAYLATER_SESSION_MODEL);
		$session->setData(self::PAYLATER_SESSION_DATA_KEY, FALSE);
	}

	/**
	 * Returns offer info text
	 * or FALSE otherwise
	 * 
	 * @return string
	 * @deprecated since changed storage to quote/order table
	 */
	public function getOfferInfoText()
	{
		$offer = $this->getCheckoutOffer();
		return array_key_exists(self::PAYLATER_SESSION_INFO_TEXT, $offer) ? $offer[self::PAYLATER_SESSION_INFO_TEXT] : FALSE;
	}

	/**
	 * Returns offer info text (used in PayLater new order emails) 
	 * or FALSE otherwise
	 * 
	 * @return string
	 * @deprecated since changed storage to quote/order table 
	 */
	public function getOfferEmailInfoText()
	{
//		$offer = $this->getCheckoutOffer();
//		return array_key_exists(self::PAYLATER_SESSION_EMAIL_INFO_TEXT, $offer) ? $offer[self::PAYLATER_SESSION_EMAIL_INFO_TEXT] : FALSE;
	}

	/**
	 * Returns true is chosen checkout payment method is PayLater
	 * 
	 * @return bool 
	 */
	public function isPayLaterPaymentMethod()
	{
		return $this->_getPaymentMethod() == self::PAYLATER_PAYMENT_METHOD ? TRUE : FALSE;
	}

	/**
	 * Returns checkout type (e.g onepage, onestep, etc)
	 * 
	 * @return string
	 */
	public function getCheckoutType()
	{
		return $this->getPayLaterConfigType('checkout');
	}

	/**
	 * 
	 * Returns representative example legal notice, replacing placeholders with store name and address
	 * 
	 * @return string
	 */
	public function getRepresentativeLegal()
	{
		$legal = $this->getPayLaterConfigLegal('product');
		return preg_replace("/[\n\r\t]/", "", $legal);
	}
	
	/**
	 * Returns store object by code, or false otherwise
	 * 
	 * @param string $storeCode
	 * @return store/boolean
	 */
	public function getStoreByCode($storeCode)
	{
		$stores = array_keys(Mage::app()->getStores());
		foreach ($stores as $id) {
			$store = Mage::app()->getStore($id);
			if ($store->getCode() == $storeCode) {
				return $store;
			}
		}
		return false;
	}
	
	public function canAccessGateway ()
	{
		$request = Mage::app()->getRequest();
		$explodedReferer = explode(DS, $request->getServer('HTTP_REFERER'));
		$mageHome = Mage::getBaseUrl();
		$refererDomain = array_key_exists(2, $explodedReferer) ? $explodedReferer[2] : false;
		return preg_match("~$refererDomain~", $mageHome);
	}
}