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
class PayLater_PayLater_Block_Adminhtml_System_Config_Refund extends Mage_Adminhtml_Block_System_Config_Form_Field
{

	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setElement($element);
		$url = $this->getUrl('paylater/adminhtml_config_update/refund');

		$has_records = Mage::getModel('paylater/refund')->hasRecordsToExport();
		if ($has_records) {
			$html = $this->getLayout()->createBlock('adminhtml/widget_button')
					->setType('button')
					->setClass('scalable refund-button')
					->setLabel('Download PayLater Refunds CSV')
					->setOnClick("setLocation('$url');disableElements('refund-button');")
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
