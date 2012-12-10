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
 * @copyright  Copyright (C) 2012 PayLater
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @subpackage Model
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class PayLater_PayLater_Model_Event_Observer implements PayLater_PayLater_Core_Interface
{

	protected function _setPriceJs($type)
	{
		$payLater = Mage::helper('paylater');
		$isEnabled = $payLater->getPayLaterConfigRunStatus('globals');
		$cache = Mage::getModel('paylater/cache_factory');
		$payLaterData = $payLater->loadCacheData($cache);
		if ($type == self::PAYLATER_TYPE_PRODUCT) {
			if ($isEnabled) {
				if (is_array($payLaterData)) {
					$currentProduct = Mage::getModel('paylater/catalog_product');
					if ($currentProduct->isWithinPayLaterRange($payLaterData)) {
						$layout = Mage::helper('paylater/layout');
						$layout->setPriceJs();
						return true;
					}
				}
			}
		} elseif ($type == self::PAYLATER_TYPE_CHECKOUT) {
			if ($isEnabled) {
				if (is_array($payLaterData)) {
					$quote = Mage::getModel('paylater/checkout_quote');
					if ($quote->isWithinPayLaterRange($payLaterData)) {
						$layout = Mage::helper('paylater/layout');
						$layout->setPriceJs();
						return true;
					}
				}
			}
		}
		return false;
	}

	public function catalogProductViewBefore(Varien_Event_Observer $observer)
	{
		$this->_setPriceJs(self::PAYLATER_TYPE_PRODUCT);
	}

	public function checkoutOnepageIndexBefore(Varien_Event_Observer $observer)
	{
		$this->_setPriceJs(self::PAYLATER_TYPE_CHECKOUT);
	}
	
	/**
	 * @deprecated
	 * 
	 * @param Varien_Event_Observer $observer 
	 */
	public function checkoutOnepageReturnBefore(Varien_Event_Observer $observer)
	{
		$onepage = Mage::getModel('paylater/checkout_onepage');
		$quote = Mage::getModel('paylater/checkout_quote');
		$onepage->getCheckout()->setStepData('billing', array('label' => Mage::helper('checkout')->__('Billing Information'),
			'is_show' => true), $quote->getBillingAddress()->getData());

		$this->_setPriceJs(self::PAYLATER_TYPE_CHECKOUT);
	}

	public function saveOrderAfter(Varien_Event_Observer $observer)
	{
		$order = $observer->getOrder();
		$quote = $observer->getQuote();	
		/**
		 * @var Mage_Sales_Model_Quote_Payment 
		 */
		$payment = $quote->getPayment();
		if ($payment->getMethod() == self::PAYLATER_PAYMENT_METHOD) {
			$order->setCanSendNewEmailFlag(false);
		}
	}

	/**
	 * @deprecated
	 * @param Varien_Event_Observer $observer 
	 */
	public function coreBlockToHtmlBefore(Varien_Event_Observer $observer)
	{
		return $this;
	}

	/**
	 * @deprecated
	 * @param Varien_Event_Observer $observer 
	 */
	public function coreBlockToHtmlAfter(Varien_Event_Observer $observer)
	{
		return $this;
	}

}
