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
 * @subpackage Block
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class PayLater_PayLater_Block_Checkout_Onepage_Review_Button extends Mage_Core_Block_Template implements PayLater_PayLater_Core_Interface, PayLater_PayLater_Core_ShowableInterface
{

	protected $_paramsMap = array(
		'reference' => '', 
		'amount' => '', 
		'merchantorderid' => '',
		'currency' => 'GBP',
		'postcode' => '',
		'items' => array()
	);
	
	protected $_paramsItemMap = array();
	
	protected function _getReference()
	{
		return $this->helper('paylater')->getPayLaterConfigReference('merchant');
	}
	
	protected function _getAmount ()
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		return $quote->getGrandTotal();
	}
	
	protected function _getPaymentMethod ()
	{
		return Mage::getModel('paylater/checkout_onepage')->getPaymentMethod();
	}

	public function getQuoteGrandTotal()
	{
		return $this->_getAmount();
	}

	public function getCustomerNote()
	{
		return Mage::helper('paylater')->getPayLaterConfigCustomerNote('review');
	}

	public function canShow()
	{
		return Mage::helper('paylater')->canShowAtCheckout();
	}

	public function getParams()
	{
		$this->_paramsMap['reference'] = $this->_getReference();
		$this->_paramsMap['amount'] = $this->_getAmount();
		return json_encode($this->_paramsMap);
	}
	
	public function getPaymentMethod ()
	{
		return $this->_getPaymentMethod();
	}
	
	public function isPayLaterPaymentMethod ()
	{
		return $this->_getPaymentMethod() == self::PAYLATER_PAYMENT_METHOD ? TRUE : FALSE;
	}
}
