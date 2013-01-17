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
class PayLater_PayLater_Model_Api_Request implements PayLater_PayLater_Core_Interface
{

	/**
	 *
	 * @var \Zend_Http_Client 
	 */
	protected $_client;

	/**
	 *
	 * @var boolean 
	 */
	protected $_setConfigFlag = true;

	/**
	 *
	 * @var array 
	 */
	protected $_config = array();

	/**
	 *
	 * @var string 
	 */
	protected $_rawData;

	/**
	 * 
	 * @return \Zend_Http_Client 
	 */
	protected function _getClient()
	{
		if (!isset($this->_client) && !($this->_client instanceof Zend_Http_Client)) {
			if (Mage::helper('paylater')->isTestEnvironment()) {
				if ($this->getSetConfigFlag() === true) {
					$this->_client = new Zend_Http_Client(self::PAYLATER_API_QUERIES_TEST, $this->_config);
				} else {
					$this->_client = new Zend_Http_Client(self::PAYLATER_API_QUERIES_TEST);
				}
				return $this->_client;
			}
			// live environment
			if ($this->getSetConfigFlag() === true) {
				$this->_client = new Zend_Http_Client(self::PAYLATER_API_QUERIES_LIVE, $this->_config);
			} else {
				$this->_client = new Zend_Http_Client(self::PAYLATER_API_QUERIES_LIVE);
			}
		}
		return $this->_client;
	}

	protected function _setRawData($merchantReference, $orderId, $xmlNs = false, $xmlNsXsi = false)
	{
		$xmlData = '<Messages xmlns="%s" xmlns:xsi="%s">';
		$xmlData .= '<GetOrderSummary>';
		$xmlData .= '<MerchantReference>';
		$xmlData .= '%s';
		$xmlData .= '</MerchantReference>';
		$xmlData .= '<MerchantOrderId>';
		$xmlData .= '%s';
		$xmlData .= '</MerchantOrderId>';
		$xmlData .= '</GetOrderSummary>';
		$xmlData .= '</Messages>';

		if ($xmlNs === false) {
			$xmlNs = self::PAYLATER_XMLNS;
		}

		if ($xmlNsXsi === false) {
			$xmlNsXsi = self::PAYLATER_XMLNS_XSI;
		}
		$this->_rawData = sprintf($xmlData, $xmlNs, $xmlNsXsi, $merchantReference, $orderId);
		return $this->_rawData;
	}

	protected function _setHeaders()
	{
		$this->_getClient()->setHeaders('MerchantReference', Mage::helper('paylater')->getMerchantReference());
	}

	public function __construct()
	{
		$this->_config = array(
			'maxredirects' => self::PAYLATER_API_REQUEST_MAXREDIRECTS,
			'timeout' => self::PAYLATER_API_REQUEST_TIMEOUT
		);
	}

	/**
	 *
	 * @return \Zend_Http_Client 
	 */
	public function getClient()
	{
		return $this->_getClient();
	}

	public function getSetConfigFlag()
	{
		return $this->_setConfigFlag;
	}

	public function setSetConfigFlag($setConfigFlag)
	{
		$this->_setConfigFlag = $setConfigFlag;
		return $this;
	}

	public function setHeaders()
	{
		$this->_setHeaders();
		return $this;
	}

	public function setMethod($method = Zend_Http_Client::POST)
	{
		$this->_getClient()->setMethod($method);
		return $this;
	}

	public function setRawData($orderId, $xmlNs = false, $xmlNsXsi = false)
	{
		$this->_setRawData(Mage::helper('paylater')->getMerchantReference(), $orderId, $xmlNs, $xmlNsXsi);
		$this->_getClient()->setRawData($this->_rawData, 'UTF-8');
		return $this;
	}

	/**
	 * For dubugging purposes
	 * 
	 * @return string 
	 */
	public function getRawData()
	{
		return $this->_rawData;
	}

}
