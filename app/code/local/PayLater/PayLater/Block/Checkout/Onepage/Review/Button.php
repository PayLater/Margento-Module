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
class PayLater_PayLater_Block_Checkout_Onepage_Review_Button extends Mage_Core_Block_Template implements PayLater_PayLater_Core_Interface, PayLater_PayLater_Core_ShowableInterface
{

	protected $_paramsMap = array(
		self::PAYLATER_PARAMS_MAP_REFERENCE_KEY => '',
		self::PAYLATER_PARAMS_MAP_AMOUNT_KEY => '',
		self::PAYLATER_PARAMS_MAP_ORDERID_KEY => '',
		self::PAYLATER_PARAMS_MAP_CURRENCY_KEY => '826',
		self::PAYLATER_PARAMS_MAP_POSTCODE_KEY => '',
		self::PAYLATER_PARAMS_MAP_ITEMS_KEY => array()
	);

	protected function _getReference()
	{
		return $this->helper('paylater')->getPayLaterConfigReference('merchant');
	}

	protected function _getAmount()
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		return $quote->getGrandTotal();
	}

	protected function _getPaymentMethod()
	{
		return Mage::getModel('paylater/checkout_onepage')->getPaymentMethod();
	}

	protected function _getPostcode()
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		return $quote->getPayLaterPostcode();
	}
	
	protected function _getPostcodes()
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		return array('billing' => $quote->getBillingPostcode(), 'shipping' => $quote->getShippingPostcode());
	}

	/**
	 * Returns quote grand total
	 * @return float 
	 */
	public function getQuoteGrandTotal()
	{
		return $this->_getAmount();
	}

	/**
	 * Returns customer note to be displayed close to PayLater payment button
	 * at checkout review step
	 * 
	 * @return string 
	 */
	public function getCustomerNote()
	{
		return Mage::helper('paylater')->getPayLaterConfigCustomerNote('review');
	}

	/**
	 * @see PayLater_PayLater_Core_ShowableInterface
	 * @return boolean 
	 */
	public function canShow()
	{
		$helper = Mage::helper('paylater');
		return $helper->canShowAtCheckout() && $helper->isAllowedCurrency();
	}

	/**
	 * Returns initial set of paramater to be posted to PayLater
	 * 
	 * @return json object 
	 */
	public function getParams()
	{
		$this->_paramsMap[self::PAYLATER_PARAMS_MAP_REFERENCE_KEY] = $this->_getReference();
		$this->_paramsMap[self::PAYLATER_PARAMS_MAP_AMOUNT_KEY] = $this->_getAmount();
		$this->_paramsMap[self::PAYLATER_PARAMS_MAP_POSTCODE_KEY] = $this->_getPostcode();
		$postcodes = $this->_getPostcodes();
		$this->_paramsMap[self::PAYLATER_PARAMS_MAP_DELIVERYPOSTCODE_KEY] = $postcodes['shipping'];
		$this->_paramsMap[self::PAYLATER_PARAMS_MAP_BILLINGPOSTCODE_KEY] = $postcodes['billing'];
		$this->_paramsMap[self::PAYLATER_PARAMS_MAP_ACTION] = $this->getBeforeEndpointAction();
		$this->_paramsMap[self::PAYLATER_PARAMS_MAP_BUTTON] = Mage::helper('paylater')->__('Pay with PayLater');
		return json_encode($this->_paramsMap);
	}

	/**
	 * Returns chechout payment method
	 * 
	 * @return string 
	 */
	public function getPaymentMethod()
	{
		return $this->_getPaymentMethod();
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
	
	public function getBeforeEndpointAction ()
	{
		return $this->getUrl(self::PAYLATER_BEFORE_ENDPOINT_ACTION, array('_secure' => true));
	}

}
