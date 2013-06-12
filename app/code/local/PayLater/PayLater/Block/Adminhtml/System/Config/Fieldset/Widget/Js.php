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
class PayLater_PayLater_Block_Adminhtml_System_Config_Fieldset_Widget_Js extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface, PayLater_PayLater_Core_Interface
{
	protected $_storeId = 0;
	
	protected function _getSourceModel($model)
	{
		return Mage::getModel('paylater/source_' . $model);
	}
	
	public function _getScopeStoreId ()
	{
        $curStore   = $this->getRequest()->getParam('store');
		$stores = Mage::app()->getStores();

		foreach ($stores as $store) {
			if ($store->getCode() == $curStore) {
				return $store->getId();
			}
		}
		return 0;
	}
	
	/**
	 * Render fieldset block html
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setTemplate(self::SYSTEM_CONFIG_WIDGET_JS_TEMPLATE);
		return $this->toHtml();
	}
	
	public function getKnockoutJsSrc ()
	{
		 return $this->getSkinUrl('paylater/paylater/js/knockout-2.2.1.js');
	}
	
	public function getDukeJsSrc ()
	{
		 return $this->getSkinUrl('paylater/paylater/js/duke.js');
	}
	
	public function getWidgetsJsSrc ()
	{
		 return $this->getSkinUrl('paylater/paylater/js/widgets.js');
	}
	
	public function getWidgetsJSON ()
	{
		 return $this->getSkinUrl('paylater/paylater/js/widgets.json');
	}
	
	public function getStoreConfiguredWidget ($type)
	{
		$widgetConfig = Mage::getStoreConfig('paylater/widgets/' . $type . '_config', $this->_getScopeStoreId());
		// revert first to PHP array
		$decodedWidgetConfig = json_decode($widgetConfig);
		return strlen(trim($widgetConfig)) > 0 ? json_encode($decodedWidgetConfig) : 'false';
	}
}