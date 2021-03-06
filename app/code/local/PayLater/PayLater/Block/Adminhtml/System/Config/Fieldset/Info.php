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
class PayLater_PayLater_Block_Adminhtml_System_Config_Fieldset_Info extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface, PayLater_PayLater_Core_Interface
{

	/**
	 * Render fieldset html
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setTemplate(self::SYSTEM_CONFIG_INFO_TEMPLATE);
		return $this->toHtml();
	}

	public function getVersion()
	{
		return (string) Mage::getConfig()->getNode(self::PAYLATER_CONFIG_NODE_VERSION);
	}

	public function getLicenseUrl()
	{
		return Mage::getConfig()->getNode(self::PAYLATER_CONFIG_NODE_LICENSE_URL);
	}

	public function getSupportEmail()
	{
		return Mage::getConfig()->getNode(self::PAYLATER_CONFIG_NODE_SUPPORT_EMAIL);
	}

	public function getReportEmail()
	{
		return Mage::getConfig()->getNode(self::PAYLATER_CONFIG_NODE_REPORT_EMAIL);
	}

	public function getDevEmail()
	{
		return Mage::getConfig()->getNode(self::PAYLATER_CONFIG_NODE_DEV_EMAIL);
	}

}
