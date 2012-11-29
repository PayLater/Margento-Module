<?php
/**
 * Wonga.com Ltd extension for Magento
 *
 * Long description of this file (if any...)
 *
 * NOTICE OF LICENSE
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Wonga PayLater module to newer versions in the future.
 * If you wish to customize the Wonga PayLater module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Wonga
 * @package    Wonga_PayLater
 * @copyright  Copyright (C) 2012 Wonga.com Ltd (http://www.wonga.com/)
 * @license    
 */

/**
 * 
 *
 * @category   Wonga
 * @package    Wonga_PayLater
 * @subpackage Block
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class Wonga_PayLater_Block_Adminhtml_System_Config_Fieldset_Info extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface, Wonga_PayLater_Model_Interface_PayLater
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
    	return (string) Mage::getConfig()->getNode('modules/Wonga_PayLater/version');
    }
	
	public function getLicenseUrl()
	{
		return Mage::getConfig()->getNode('license/url');
	}
	
	public function getSupportEmail()
	{
		return Mage::getConfig()->getNode('support/email');
	}
	
	
	public function getReportEmail()
	{
		return Mage::getConfig()->getNode('report/bugs');
	}
	
	public function getDevEmail()
	{
		return Mage::getConfig()->getNode('dev/email');
	}
}
