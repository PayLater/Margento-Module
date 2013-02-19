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
		$layout = Mage::helper('paylater/layout')->getCoreLayout();
		$onepage = $layout->getBlock('checkout.onepage');
		$messages = $layout->createBlock(
				self::PAYLATER_CHECKOUT_ONEPAGE_MESSAGES_BLOCK, 'paylater.checkout.onepage.messages'
		);
		$onepage->setMessagesBlock($messages);
		$onepage->setTemplate(Mage::helper('paylater')->getPayLaterConfigOnepageIndex('template'));
	}

	public function onestepCheckoutIndexBefore(Varien_Event_Observer $observer)
	{
		$this->_setPriceJs(self::PAYLATER_TYPE_CHECKOUT);
		$layout = Mage::helper('paylater/layout');
		$coreLayout = $layout->getCoreLayout();
		$onestep = $coreLayout->getBlock('onestepcheckout.checkout');
		$onestep->setTemplate(Mage::helper('paylater')->getPayLaterConfigOnestepIndex('template'));
		$messages = $coreLayout->createBlock(
				self::PAYLATER_CHECKOUT_ONEPAGE_MESSAGES_BLOCK, 'paylater.checkout.onepage.messages'
		);
		$onestep->setMessagesBlock($messages);
		$onestepPaymentMethod = $coreLayout->getBlock('choose-payment-method');
		$onestepPaymentMethod->setTemplate(Mage::helper('paylater')->getPayLaterConfigOnestepPaymentMethod('template'));
		$layout->setOnestepJs();
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

	public function adminSystemConfigChanged(Varien_Event_Observer $observer)
	{
		try {
			$config = Mage::helper('paylater')->getCoreConfig();
			$storeCode = $observer->getStore();
			if (!$storeCode) {
				$storeCode = Mage_Core_Model_App::DISTRO_STORE_CODE;
				$storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
			} else {
				$storeId = Mage::helper('paylater')->getStoreByCode($storeCode)->getId();
			}
			if ((int) Mage::helper('paylater')->getPayLaterConfigRunStatus('globals', $storeCode) === 0) {
				$config->saveConfig('payment/paylater/active', 0, $storeCode, $storeId);
			} else {
				$config->saveConfig('payment/paylater/active', 1, $storeCode, $storeId);
			}
		} catch (Exception $e) {
			Mage::helper('paylater')->log(Mage::helper('paylater')->__($e->getMessage()), __METHOD__, Zend_Log::ERR);
		}
	}
}
