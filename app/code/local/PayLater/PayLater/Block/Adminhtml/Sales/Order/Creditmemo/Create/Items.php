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
class PayLater_PayLater_Block_Adminhtml_Sales_Order_Creditmemo_Create_Items extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items
{

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

	/**
	 * Prepare child blocks
	 *
	 * @return Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items
	 */
	protected function _prepareLayout()
	{
		$return = parent::_prepareLayout();
		if ($this->getCreditmemo()->canRefund()) {
			if ($this->getCreditmemo()->getInvoice() && $this->getCreditmemo()->getInvoice()->getTransactionId()) {
				$label = ($this->_helper('creditmemo')->isPayLaterPayment($this->getOrder()->getPayment())) ? Mage::helper('sales')->__('PayLater Refund') : Mage::helper('sales')->__('Refund');
				$this->getChild('submit_button')->setLabel($label);
			}
		}
		return $return;
	}

}