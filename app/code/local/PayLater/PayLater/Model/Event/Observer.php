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

	/**
	 * 
	 * @param string $type
	 * @return boolean
	 * 
	 */
	protected function _setPriceJs($type)
	{
		return $this->_setWidgetJs($type);
		/**
		 * @deprecated since version 2.0.0
		 */
//		$payLater = Mage::helper('paylater');
//		$isEnabled = $payLater->getPayLaterConfigRunStatus('globals');
//		$cache = Mage::getModel('paylater/cache_factory');
//		$payLaterData = $payLater->loadCacheData($cache);
//		if ($type == self::PAYLATER_TYPE_PRODUCT) {
//			if ($isEnabled) {
//				if (is_array($payLaterData)) {
//					$currentProduct = Mage::getModel('paylater/catalog_product');
//					if ($currentProduct->isWithinPayLaterRange($payLaterData)) {
//						$layout = Mage::helper('paylater/layout');
//						$layout->setPriceJs();
//						return true;
//					}
//				}
//			}
//		} elseif ($type == self::PAYLATER_TYPE_CHECKOUT) {
//			if ($isEnabled) {
//				if (is_array($payLaterData)) {
//					$quote = Mage::getModel('paylater/checkout_quote');
//					if ($quote->isWithinPayLaterRange($payLaterData)) {
//						$layout = Mage::helper('paylater/layout');
//						$layout->setPriceJs();
//						return true;
//					}
//				}
//			}
//		}
//		return false;
	}
	
	protected function _setWidgetJs($type)
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
						$layout->setWidgetJs();
						return true;
					} else {
                        // loads anyway for configurable/bundle products
                        $layout = Mage::helper('paylater/layout');
                        $layout->setWidgetJs();
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
						$layout->setWidgetJs();
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
	
	public function checkoutCartIndexBefore (Varien_Event_Observer $observer)
	{
		$payLater = Mage::helper('paylater');
		if ($payLater->getPayLaterConfigShowOnCart('widgets') && Mage::helper('checkout/cart')->getCart()->getItemsCount() > 0) {
			$this->_setWidgetJs(self::PAYLATER_TYPE_CHECKOUT);
			$layout = Mage::helper('paylater/layout');
			$coreLayout = $layout->getCoreLayout();
			//$coreLayout->getBlock('checkout.cart')->setTemplate('paylater/paylater/checkout/cart/cart.phtml');
			$coreLayout->getBlock('checkout.cart')->setTemplate($payLater->getPayLaterConfigTemplate('cart'));
		}
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

	/**
	 * Poll recent orphaned transactions against PayLater
	 */
	public function orphanedOrderPolling()
	{
		$helper = Mage::helper('paylater');
		$now =  Mage::getModel('core/date')->timestamp(time());
		$from = date("Y-m-d H:i:s", strtotime("-4 hours", $now));
		$to = date("Y-m-d H:i:s", strtotime("-15 minutes", $now));

		$helper->log("Started polling cron... Searching From: $from To: $to", __METHOD__, Zend_Log::DEBUG);

		$orders = Mage::getModel('sales/order')->getCollection()
				->addAttributeToFilter('state', self::PAYLATER_ORPHANED_ORDER_STATE)
				->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to));

		$helper->log("Found '" . count($orders) . "' orphaned transactions", __METHOD__, Zend_Log::DEBUG);

		foreach ($orders as $order) {
			$orderId = $order->getIncrementId();
			$helper->log("Polling... " . $orderId, __METHOD__, Zend_Log::DEBUG);

			$paylater_order = Mage::getModel('paylater/sales_order', array($orderId));
			$apiRequest = Mage::getModel('paylater/api_request');

			$apiRequest->setHeaders()->setMethod()->setRawData($orderId);
			$apiResponse = Mage::getModel('paylater/api_response', array($apiRequest));
			$helper->log("\tResponse: " . $apiResponse->getStatus(), __METHOD__, Zend_Log::DEBUG);

			if ($apiResponse->isSuccessful() && $apiResponse->getStatus() == self::PAYLATER_API_ACCEPTED_RESPONSE && $apiResponse->doesAmountMatch($paylater_order)) {
				$paylater_order->setStateAndStatus();
				$paylater_order->savePayLaterOrderStatus($apiResponse->getStatus());
				$paylater_order->save();
				if ($paylater_order->invoice()) {
					$paylater_order->sendEmail();
				}
				$paylater_order->setInactiveQuote();
				
				// Attempt to clear checkout session to be safe
				try {
					$additional = unserialize($order->getPaylaterAdditional());
					$session = Mage::getModel('checkout/session')->setSessionId($additional['checkout_session_id'])->clear();
					$helper->log("\tSuccessfully cleared session", __METHOD__, Zend_Log::DEBUG);
				} catch (Exception $e) {
					$helper->log("\tLost track of session", __METHOD__, Zend_Log::ERR);
				}

				Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($orderId)));
			}else if (!trim($apiResponse->getStatus())) {
                $order->setStateAndStatus(trim($apiResponse->getStatus()));
                $order->savePayLaterOrderStatus(self::PAYLATER_API_PENDING_RESPONSE);
                $order->save();
                Mage::dispatchEvent('empty_paylater_status_response', array('response' => $apiResponse->getStatus(), 'orderId' => $orderId));
                $helper->log("\tEmpty Response: " . $apiResponse->getStatus(), __METHOD__, Zend_Log::DEBUG);
                return;
            } else {
				$paylater_order->savePayLaterOrderStatus($apiResponse->getStatus());
				$paylater_order->setStateAndStatus($apiResponse->getStatus());
				$paylater_order->save();
			}
			Mage::dispatchEvent('paylater_response', array('response' => $apiResponse->getStatus(), 'orderId' => $orderId));
		}
	}

}
