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
class PayLater_PayLater_Model_Payment_Gateway extends Mage_Payment_Model_Method_Abstract implements PayLater_PayLater_Core_Interface
{
	/**
	 * Payment code name
	 *
	 * @var string
	 */
	protected $_code = 'paylater';
	protected $_isGateway = true;
	protected $_canCapture = true;
    protected $_canCapturePartial = false;
	protected $_canRefund = true;
	protected $_canUseInternal = false;
	protected $_canRefundInvoicePartial = true;
	protected $_canUseForMultishipping = false;
	protected $_formBlockType = 'paylater/payment_form';
	protected $_infoBlockType = 'paylater/payment_info';

	/**
	 *
	 * @param type $type
	 * @return boolean|PayLater_PayLater_Helper_Data
	 */
	protected function _helper($type = "data")
	{
		if ($type) {
			return ($type != "data") ? Mage::helper('paylater/' . $type) : Mage::helper('paylater');
		}

		return FALSE;
	}

	public function capture(Varien_Object $payment, $amount)
	{
		$order = Mage::getModel('sales/order')->load($payment->getParentId());
		if ($order->getId()) {
			$payment
					->setTransactionId($this->_helper()->getPayLaterConfigReference('merchant') . '-' . $order->getIncrementId())
					->setIsTransactionClosed(0);
		}
		return $this;
	}

	public function refund(Varien_Object $payment, $amount)
	{
		$refund = Mage::getModel('paylater/refund');
		if (!$refund->createRefundRecord($amount)) {
			$this->_getSession()->addError('Error creating refund record, unable to perform a PayLater Refund');
			$this->_redirectReferer();
		}
		return parent::refund($payment, $amount);
	}

	/**
	 * Return Order place redirect url as boolean
	 *
	 * @return boolean
	 */
	public function getOrderPlaceRedirectUrl()
	{
		return true;
	}

}
