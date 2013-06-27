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
class PayLater_PayLater_Block_Payment_Form extends Mage_Core_Block_Template implements PayLater_PayLater_Core_Interface, PayLater_PayLater_Core_ShowableInterface, PayLater_PayLater_Core_TypeableInterface, PayLater_PayLater_Core_WidgetInterface
{

    protected $_widgetJson;
    protected $_widgetConfig;
    protected $_widgetName;
    protected $_widgetShowBreakdown;
    protected $_widgeParams;
    protected $_widgeParamArray;

    protected function _construct()
	{
		parent::_construct();
		$this->setTemplate(self::PAYMENT_METHOD_FORM_TEMPLATE);
	}

	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		if ($this->canShow() === false && Mage::helper('paylater')->isAllowedCurrency()) {
			$this->setTemplate(self::BASKET_NOT_IN_RANGE_TEMPLATE);
		}
		if (!$this->getTemplate()) {
			return '';
		}
		$html = $this->renderView();
		return $html;
	}

	/**
	 *
	 * @return float 
	 */
	public function getGrandTotal()
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		return $quote->getGrandTotal();
	}

	/**
	 * @see PayLater_PayLater_Core_TypeableInterface
	 * @return string 
	 */
	public function getPayLaterType()
	{
		return self::PAYLATER_TYPE_CHECKOUT;
	}

	/**
	 * @see PayLater_PayLater_Core_ShowableInterface
	 * @return boolean 
	 */
	public function canShow()
	{
		$helper = Mage::helper('paylater');
		return $helper->canShowAtCheckout() && $helper->isAllowedCurrency();
	}

    public function hasWidgetBeenConfigured ()
    {
        return strlen(trim($this->getConfiguredWidgetJSON()));
    }

    public function getConfiguredWidgetJSON ()
    {
        $this->_widgetJson = Mage::helper('paylater')->getPayLaterConfigCheckoutConfig('widgets');
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
        return $this->getGrandTotal();
    }

    public function isConfiguredWidgetSameAsDefault ()
    {
        $widgetsDefault = file_get_contents(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . self::PAYLATER_WIDGET_JSON_AMDINHTML_LOCATOR);
        $decodedWidgetsDefault = Zend_Json_Decoder::decode($widgetsDefault);
        $defaultWidgetParams = (array) $decodedWidgetsDefault[$this->_widgetName];
        unset ($defaultWidgetParams['widget-image']);
        $defaultWidgetParamsObj = new ArrayObject($defaultWidgetParams);
        $configWidgetParamsObj = new ArrayObject($this->_widgeParamArray);
        return ($defaultWidgetParamsObj == $configWidgetParamsObj);
    }
}
