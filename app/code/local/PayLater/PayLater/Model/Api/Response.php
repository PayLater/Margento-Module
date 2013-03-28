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
class PayLater_PayLater_Model_Api_Response implements PayLater_PayLater_Core_Interface
{

	/**
	 *
	 * @var Zend_Http_Client 
	 */
	protected $_request;

	/**
	 *
	 * @var Zend_Http_Response 
	 */
	protected $_response;

	/**
	 *
	 * @var SimpleXMLElement 
	 */
	protected $_responseXml;

	/**
	 * Possible valid statuses
	 */
	protected $_statuses = array(
		self::PAYLATER_API_ACCEPTED_RESPONSE,
		self::PAYLATER_API_DECLINED_RESPONSE,
		self::PAYLATER_API_CANCELED_RESPONSE,
		self::PAYLATER_API_PENDING_RESPONSE
	);

	/**
	 * Protected request getter
	 * 
	 * @return Zend_Http_Client 
	 */
	protected function _getRequest()
	{
		return $this->_request;
	}

	protected function _setResponse()
	{
		$this->_response = $this->_getRequest()->getClient()->request();
	}

	/**
	 *
	 * @return SimpleXMLElement 
	 */
	protected function _getResponseXml()
	{
		if (!isset($this->_responseXml)) {
			$this->_responseXml = new SimpleXMLElement($this->_response->getBody());
		}
		return $this->_responseXml;
	}

	public function __construct(array $arguments)
	{
		if (!array_key_exists(0, $arguments) && !($arguments[0] instanceof Zend_Http_Client)) {
			throw new PayLater_PayLater_Exception_InvalidArguments(__METHOD__ . ' ' . Mage::helper('paylater')->__('Invalid arguments. First argument must be instance of Zend_Http_Client'));
		}
		$this->_request = $arguments[0];
		$this->_setResponse();
	}

	/**
	 * 
	 * @return Zend_Http_Response 
	 */
	public function getResponse()
	{
		return $this->_response;
	}

	/**
	 * Response has a valid status
	 * @return boolean
	 */
	public function hasStatus()
	{
		if ($this->getStatus() && in_array($this->getStatus(), $this->_statuses)) {
			return True;
		}

		return False;
	}

	/**
	 * 
	 * @return SimpleXMLElement 
	 */
	public function getResponseXml()
	{
		return $this->_getResponseXml();
	}

	public function getStatus()
	{
		return mb_convert_case($this->_getResponseXml()->{self::PAYLATER_SUMMARY_RESPONSE_NODE}->{self::PAYLATER_SUMMARY_RESPONSE_STATUS_NODE}, MB_CASE_LOWER);
	}

	public function getAmount()
	{
		return $this->_getResponseXml()->{self::PAYLATER_SUMMARY_RESPONSE_NODE}->{self::PAYLATER_SUMMARY_RESPONSE_AMOUNT_NODE};
	}

	public function getPostcode()
	{
		return $this->_getResponseXml()->{self::PAYLATER_SUMMARY_RESPONSE_NODE}->{self::PAYLATER_SUMMARY_RESPONSE_POSTCODE_NODE};
	}

	/**
	 *
	 * @return boolean 
	 */
	public function isSuccessful()
	{
		return $this->_response->isSuccessful();
	}

	public function doesAmountMatch(PayLater_PayLater_Model_Sales_Order $order)
	{
		return $this->getAmount() == $order->getInstance()->getTotalDue();
	}

}
