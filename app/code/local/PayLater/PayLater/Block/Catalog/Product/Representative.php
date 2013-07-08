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
class PayLater_PayLater_Block_Catalog_Product_Representative extends Mage_Core_Block_Template implements PayLater_PayLater_Core_Interface, PayLater_PayLater_Core_ShowableInterface, PayLater_PayLater_Core_TypeableInterface, PayLater_PayLater_Core_WidgetInterface
{

    protected $_widgetJson;
    protected $_widgetConfig;
    protected $_widgetName;
    protected $_widgetShowBreakdown;
    protected $_widgeParams;
    protected $_widgeParamArray;

    /**
	 * @see PayLater_PayLater_Core_ShowableInterface
	 * @return boolean 
	 */
	public function canShow()
	{
		$helper = Mage::helper('paylater');
		return $helper->canShowOnProduct() && $helper->isAllowedCurrency();
	}

	public function getProductPrice()
	{
		$currentProduct = Mage::registry('product');
		return $currentProduct->getPrice();
	}
	
	public function getProductFinalPrice()
	{
		$currentProduct = Mage::registry('product');
		return $this->helper('tax')->getPrice($currentProduct, $currentProduct->getFinalPrice());
	}
	
	public function isConfigurableProduct()
	{
		$currentProduct = Mage::registry('product');
		return $currentProduct->isConfigurable();
	}

	/**
	 * @see PayLater_PayLater_Core_TypeableInterface
	 * @return string 
	 */
	public function getPayLaterType()
	{
		return self::PAYLATER_TYPE_PRODUCT;
	}

	/**
	 * Returns event type as defined in system config
	 * @return string|null 
	 */
	public function getEventType()
	{
		return Mage::helper('paylater')->getPayLaterConfigEvent('product');
	}

    public function hasWidgetBeenConfigured ()
    {
        return strlen(trim($this->getConfiguredWidgetJSON()));
    }

    public function getConfiguredWidgetJSON ()
    {
        $this->_widgetJson = Mage::helper('paylater')->getPayLaterConfigProductConfig('widgets');
        return $this->_widgetJson;
    }

    public function getConfiguredWidget ()
    {
        $this->_widgetConfig = (array) json_decode($this->getConfiguredWidgetJSON());
        try {
            $this->_widgetName = $this->_widgetConfig['name'];
            foreach ($this->_widgetConfig['fields'] as $field) {

                if ($field->key == 'show-breakdown') {
                    $this->_widgetShowBreakdown = $field->value;
                    $this->_widgeParamArray[$field->key] = $field->value == 'true' ? true : false;
                } else {
                    $this->_widgeParams[] = "{$field->key}:{$field->value}";
                    $this->_widgeParamArray[$field->key] = $field->value;
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

    public function getAmount ()
    {
        return $this->getProductFinalPrice();
    }

    public function isConfiguredWidgetSameAsDefault ()
    {
        $widgetsDefault = file_get_contents(self::PAYLATER_WIDGET_JSON_AMDINHTML_LOCATOR);
        $decodedWidgetsDefault = json_decode($widgetsDefault, true);
        $defaultWidgetParams = $decodedWidgetsDefault[$this->_widgetName];
        unset ($defaultWidgetParams['widget-image']);
        return count(array_diff($defaultWidgetParams, $this->_widgeParamArray)) > 0 ? false : true;
    }
}
