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
 * @subpackage Block
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class PayLater_PayLater_CheckoutController extends Mage_Core_Controller_Front_Action implements PayLater_PayLater_Core_Interface
{

	/**
	 *
	 * @return PayLater_PayLater_Model_Checkout_Onepage 
	 */
	protected function _getOnepage()
	{
		return Mage::getModel('paylater/checkout_onepage')->getSingleton();
	}

	/**
	 * @deprecated
	 * 
	 * @return array 
	 */
	protected function _collectAllItems()
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		$items = $quote->getAllItems();
		$all = array();
		foreach ($items as $item) {
			$arrayItem = array();
			$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_KEY] = $item->getSku();
			$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_DESCRIPTION_KEY] = $item->getDescription();
			$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_QTY_KEY] = $item->getQty();
			$arrayItem[self::PAYLATER_PARAMS_MAP_ITEM_ID_PRICE_KEY] = $item->getPrice();
			$all[] = $arrayItem;
		}
		return $all;
	}

	/**
	 * @deprecated
	 * 
	 * @return string
	 */
	protected function _outputPayLaterFormBlock()
	{
		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		$update->load('paylater_checkout_form');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}

	/**
	 * Sets PayLater order state and status and return order id, or false otherwise
	 * 
	 * @param string $state
	 * @param string $status
	 * 
	 * @return int|bool 
	 */
	protected function _setPayLaterOrderStateAndStatus($state, $status)
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		$orderId = $quote->getReservedOrderId();
		$order = Mage::getModel('paylater/checkout_onepage')->getOrder($orderId);
		$order->setState($state);
		$order->setStatus($status);
		$order->save();
		return $orderId;
	}

	protected function _redirectError($errorCode)
	{
		$helper = Mage::helper('paylater');
		$session = Mage::getSingleton('checkout/session');
		if ($errorCode && $errorCode > 0) {
			$session->addError($helper->__($helper->getPayLaterConfigErrorCodeBody('payment')));
			$helper->log($helper->getErrorMessageByCode($errorCode), __METHOD__, Zend_Log::ERR);
			$this->_setPayLaterOrderStateAndStatus(self::PAYLATER_FAILED_ORDER_STATE, self::PAYLATER_FAILED_ORDER_STATUS);
			$this->_redirect(self::PAYLATER_POST_RETURN_ERROR_LINK, array('_secure'=>true));
		}
	}
	
	protected function _toOnepageSuccess ()
	{
		$this->_redirect('checkout/onepage/success', array('_secure'=>true));
	}
	
	protected function _saveOffer ($offer)
	{
		$session = Mage::getSingleton(self::PAYLATER_SESSION_MODEL);
		$session->setData(self::PAYLATER_SESSION_DATA_KEY, $offer);
	}

	/**
	 * Saves order and sets its status and state to PayLater Orphaned 
	 * 
	 * Redirects to PAYLATER_POST_RETURN_ERROR_LINK if any exception occur while 
	 * saving the order.
	 * 
	 */
	public function gatewayAction()
	{
		$onepage = $this->_getOnepage();
		$quote = $onepage->getQuote();
		$quote->collectTotals()->save();
		$paylaterData = $this->getRequest()->getPost();
		$helper = Mage::helper('paylater');
		try {
			// stops sending order email for order in saveOrder
			if ($onepage->saveOrder()) {
				try {
					// Order was saved without sending any customer email.
					// @see Observer->saveOrderAfter
					$orderId = $this->_setPayLaterOrderStateAndStatus(self::PAYLATER_ORPHANED_ORDER_STATUS, self::PAYLATER_ORPHANED_ORDER_STATUS);
					$paylaterData[self::PAYLATER_PARAMS_MAP_ORDERID_KEY] = $orderId;
					$this->loadLayout();
					$this->_initLayoutMessages('customer/session');
					$this->getLayout()->getBlock('head')->setTitle($this->__(self::PAYLATER_GATEWAY_TITLE));
					$this->renderLayout();
					$helper->log('Saved order with id ' . $orderId, __METHOD__);
				} catch (PayLater_PayLater_Exception_InvalidHttpClientResponse $e) {
					/**
					 * @deprecated catch 
					 * 
					 * Nowhere is thrown a PayLater_PayLater_Exception_InvalidHttpClientResponse
					 */
					$helper->log($e->getMessage(), __METHOD__, Zend_Log::ERR);
					$this->_redirect(self::PAYLATER_POST_RETURN_ERROR_LINK, array('_secure'=>true));
					return;
				} catch (Mage_Core_Exception $e) {
					$helper->log($e->getMessage(), __METHOD__, Zend_Log::ERR);
					$this->_redirect(self::PAYLATER_POST_RETURN_ERROR_LINK, array('_secure'=>true));
					return;
				} catch (Exception $e) {
					$helper->log($e->getMessage(), __METHOD__, Zend_Log::ERR);
					$this->_redirect(self::PAYLATER_POST_RETURN_ERROR_LINK, array('_secure'=>true));
					return;
				}
			}
		} catch (Mage_Core_Exception $e) {
			$helper->log($e->getMessage(), __METHOD__, Zend_Log::ERR);
			$this->_redirect(self::PAYLATER_POST_RETURN_ERROR_LINK, array('_secure'=>true));
			return;
		}
	}

	public function continueAction()
	{
		/**
		 * var PayLater_PayLater_Helper_Data 
		 */
		$helper = Mage::helper('paylater');
		$params = $this->getRequest()->getParams();
		$errorCode = $this->getRequest()->getParam('ErrorCodes');
		if (!$params) {
			// can be either error at PayLater (customer clicked returned link at PayLater)
			// or success (also no params are passed)
			$orderId = Mage::getModel('paylater/checkout_quote')->getReservedOrderId();

			if ($orderId) {
				try {
					$order = Mage::getModel('paylater/sales_order', array($orderId));
					$apiRequest = Mage::getModel('paylater/api_request');
					$apiRequest->setHeaders()->setMethod()->setRawData($orderId);
					$apiResponse = Mage::getModel('paylater/api_response', array($apiRequest));
					
					if ($apiResponse->isSuccessful() && $apiResponse->getStatus() == self::PAYLATER_API_ACCEPTED_RESPONSE && $apiResponse->doesAmountMatch($order)) {
						$order->setStateAndStatus();
						$order->save();
						if ($order->invoice()) {
							$order->sendEmail();
						}
						$order->setInactiveQuote();
						$this->_toOnepageSuccess();
						return;
					} else {
						$this->_redirectError(self::ERROR_CODE_GENERIC);
						return;
					}
				} catch (PayLater_PayLater_Exception_InvalidArguments $e) {
					$helper->log($e->getMessage(), __METHOD__, Zend_Log::ERR);
					$this->_redirectError(self::ERROR_CODE_GENERIC);
					return;
				} catch (Mage_Core_Exception $e) {
					$helper->log($e->getMessage(), __METHOD__, Zend_Log::ERR);
					$this->_redirectError(self::ERROR_CODE_GENERIC);
					return;
				} catch (Exception $e) {
					$helper->log($e->getMessage(), __METHOD__, Zend_Log::ERR);
					$this->_redirectError(self::ERROR_CODE_GENERIC);
					return;
				}
			}
			$this->_redirectError(self::ERROR_CODE_GENERIC);
			return;
		}
		if ($errorCode && $errorCode > 0) {
			$this->_redirectError($errorCode);
			return;
		}
	}
	
	public function saveOfferAction ()
	{
		// unset any previous value before saving offer
		
		Mage::helper('paylater')->unsetCheckoutOffer();
		if ($this->getRequest()->isPost()) {
			$offer = $this->getRequest()->getPost();
			$this->_saveOffer($offer);
			$this->getResponse()->setBody(self::PAYLATER_SAVE_OFFER_SUCCESS);
			return;
		}
		$this->getResponse()->setBody(self::PAYLATER_SAVE_OFFER_FAILURE);
	}
}