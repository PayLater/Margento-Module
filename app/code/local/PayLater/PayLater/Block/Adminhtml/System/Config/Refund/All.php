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
 * @copyright  Copyright (C) 2013 PayLater
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
 * @author     GPMD <dev@gpmd.co.uk>
 */
class PayLater_PayLater_Block_Adminhtml_System_Config_Refund_All extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setElement($element);
		$url = $this->getUrl('paylater/adminhtml_config_update/refundAll');

		$has_records = Mage::getModel('paylater/refund')->getResourceCollection()->count();
		if ($has_records) {
			$html = $this->getLayout()->createBlock('adminhtml/widget_button')
					->setType('button')
					->setClass('scalable refund-all-button')
					->setLabel('Download Entire Refund History')
					->setOnClick("setLocation('$url');")
					->toHtml();
		} else {
			$html = $this->getLayout()->createBlock('adminhtml/widget_button')
					->setType('button')
					->setClass('scalable disabled')
					->setLabel('No PayLater Refunds To Export')
					->toHtml();
		}

		return $html;
	}
}
