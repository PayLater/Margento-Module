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
class PayLater_PayLater_Block_Adminhtml_Sales_Order_Creditmemo_Create_Items extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items
{
	/**
	 *
	 * @param type $type
	 * @return boolean|PayLater_PayLater_Helper_Data
	 */
    protected function _helper($type = "data")
	{
        if($type){
			return ($type != "data") ? Mage::helper('paylater/'.$type) : Mage::helper('paylater');
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
		$label = ($this->_helper('creditmemo')->isPayLaterPayment($this->getOrder()->getPayment())) ? Mage::helper('sales')->__('PayLater Refund') : Mage::helper('sales')->__('Refund');
		$this->getChild('submit_button')->setLabel($label);
		return $return;
    }

}