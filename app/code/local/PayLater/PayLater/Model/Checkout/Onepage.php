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
class PayLater_PayLater_Model_Checkout_Onepage
{

	protected function _getSingleton()
	{
		return Mage::getSingleton('checkout/type_onepage');
	}

	protected function _getPaymentMethod()
	{
		return $this->_getSingleton()->getQuote()->getPayment()->getMethod();
	}

	/**
	 * Returns onepage checkout chosen payment method
	 * 
	 * @return string 
	 */
	public function getPaymentMethod()
	{
		return $this->_getPaymentMethod();
	}

	/**
	 *
	 * @return Mage_Checkout_Model_Type_Onepage 
	 */
	public function getSingleton()
	{
		return $this->_getSingleton();
	}

	/**
	 *
	 * @return Mage_Checkout_Model_Session 
	 */
	public function getCheckout()
	{
		return $this->_getSingleton()->getCheckout();
	}

	/**
	 *
	 * Loads order by id
	 * 
	 * @param int $id
	 * @return Mage_Sales_Model_Order 
	 */
	public function getOrder($id)
	{
		return Mage::getModel('sales/order')->load($id, 'increment_id');
	}

}
