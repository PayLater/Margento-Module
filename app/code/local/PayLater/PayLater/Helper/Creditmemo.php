<?php

/**
 * PayLater extension for Magento
 *
 * Long description of this file (if any...)
 *
 * NOTICE OF LICENSE
 *
 * [TO BE DEFINED]
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Wonga PayLater module to newer versions in the future.
 * If you wish to customize the PayLater module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @copyright  Copyright (C) 2012 PayLater
 * @license    [TO BE DEFINED]
 */

/**
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @subpackage Helper
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class PayLater_PayLater_Helper_Creditmemo extends Mage_Core_Helper_Data implements PayLater_PayLater_Core_Interface
{
	/**
	 * Check if payment was made via paylater
	 * 
	 * @param Mage_Sales_Model_Order_Payment $payment
	 * @return boolean
	 */
	public function isPaylaterPayment(Mage_Sales_Model_Order_Payment $payment)
	{
		if($payment->getMethod() == PayLater_PayLater_Core_Interface::PAYLATER_PAYMENT_METHOD){
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Get save url for creditmemo form in adminhtml
	 * 
	 * @return string
	 */
	public function getSaveUrl()
	{
		return Mage::getUrl('*/*/paylaterSave', array('_current' => true));
	}
}