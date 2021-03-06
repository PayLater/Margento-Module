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
		if ($payment->getMethod() == PayLater_PayLater_Core_Interface::PAYLATER_PAYMENT_METHOD) {
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