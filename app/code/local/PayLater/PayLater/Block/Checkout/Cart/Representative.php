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
class PayLater_PayLater_Block_Checkout_Cart_Representative extends Mage_Checkout_Block_Cart_Abstract implements PayLater_PayLater_Core_WidgetInterface, PayLater_PayLater_Core_ShowableInterface
{
	
	protected $_widgetJson;
	protected $_widgetConfig;
	protected $_widgetName;
	protected $_widgetShowBreakdown;
	protected $_widgeParams;
	protected $_quote;
	
	protected function _getAmount()
	{
		if (isset($this->_quote)) {
			return $this->_quote->getGrandTotal();
		}
		$this->_quote = Mage::getModel('paylater/checkout_quote');
		return $this->_quote->getGrandTotal();
	}
	
	
	public function getConfiguredWidgetJSON ()
	{
		$this->_widgetJson = Mage::helper('paylater')->getPayLaterConfigCartConfig('widgets');
		return $this->_widgetJson;
	}
	
	public function getConfiguredWidget ()
	{
		if (isset($this->_widgetJson)) {
			$this->_widgetConfig = (array) json_decode($this->_widgetJson);
		} else {
			$this->_widgetConfig = (array) json_decode($this->getConfiguredWidgetJSON());
		}
		try {
			$this->_widgetName = $this->_widgetConfig['name'];
			foreach ($this->_widgetConfig['fields'] as $field) {
				
				if ($field->key == 'show-breakdown') {
					$this->_widgetShowBreakdown = $field->value;
				} else {
					$this->_widgeParams[] = "{$field->key}:{$field->value}";
				}
			}
		}catch (Exception $e) {
			/**
			 * @todo log
			 */
		}
		
		return $this->_widgetConfig;
	}
	
	public function getWidgetName()
	{
		return $this->_widgetName;
	}
	
	public function doesWidgetHaveBreakdown ()
	{
		return isset($this->_widgetShowBreakdown) && $this->_widgetShowBreakdown == 'true';
	}
	
	public function doesWidgetHaveParameters ()
	{
		return (isset($this->_widgeParams) && count($this->_widgeParams) > 0);
	}

	public function getWidgetShowBreakdown($type = 'string')
	{
		if ($type == 'string') {
			return $this->_widgetShowBreakdown;
		}
		return $this->_widgetShowBreakdown == 'true' ? true : false;
	}
	
	public function getWidgetParamsAsString()
	{
		return implode(';', $this->_widgeParams);
	}
	
	public function canShow() 
	{
		$helper = Mage::helper('paylater');
		return $helper->canShowOnCart() && $helper->isAllowedCurrency();
	}
	
	public function getAmount ()
	{
		return $this->_getAmount();
	}
}