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
class PayLater_PayLater_CheckoutController extends Mage_Core_Controller_Front_Action implements PayLater_PayLater_Core_Interface
{

	protected $_requiredGatewayPostKeys = array('reference', 'currency', 'amount', 'postcode', 'billingpostcode');

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

	/**
	 * Saves PayLater offer to order
	 * 
	 * @param type $orderId
	 */
	protected function _setOrderPayLaterOffer($orderId)
	{
		$offer = $this->_getPayLaterOfferArray();
		$order = Mage::getModel('paylater/sales_order', array($orderId));
		$order->savePayLaterOffer($offer);
	}

	protected function _redirectError($errorCode, $error = false, $setFailedState = true)
	{
		$helper = Mage::helper('paylater');
		$session = Mage::getSingleton('checkout/session');
		if ($errorCode && $errorCode > 0) {
			if ($error) {
				$session->addError($helper->__($error));
			} else {
				$session->addError($helper->__($helper->getPayLaterConfigErrorCodeBody('payment')));
			}
			$helper->log($helper->getErrorMessageByCode($errorCode), __METHOD__, Zend_Log::ERR);
			if ($setFailedState === true) {
				$this->_setPayLaterOrderStateAndStatus(self::PAYLATER_FAILED_ORDER_STATE, self::PAYLATER_FAILED_ORDER_STATUS);
			}
			if ($helper->getCheckoutType() == self::PAYLATER_CHECKOUT_TYPE_ONEPAGE) {
				$this->_redirect(self::PAYLATER_POST_RETURN_ERROR_LINK, array('_secure' => true));
			} else if ($helper->getCheckoutType() == self::PAYLATER_CHECKOUT_TYPE_ONESTEP) {
				$this->_redirect(self::PAYLATER_ONESTEP_POST_RETURN_ERROR_LINK, array('_secure' => true));
			}
		}
	}

	protected function _redirectGatewayError($error)
	{
		$helper = Mage::helper('paylater');
		Mage::getSingleton('checkout/session')->addError($helper->__($error));
		$helper->log('PayLater gateway error: ' . $error, __METHOD__);
		if ($helper->getCheckoutType() == self::PAYLATER_CHECKOUT_TYPE_ONEPAGE) {
			$this->_redirect(self::PAYLATER_POST_RETURN_ERROR_LINK, array('_secure' => true));
		} else if ($helper->getCheckoutType() == self::PAYLATER_CHECKOUT_TYPE_ONESTEP) {
			$this->_redirect(self::PAYLATER_ONESTEP_POST_RETURN_ERROR_LINK, array('_secure' => true));
		}
	}

	protected function _toOnepageSuccess()
	{
		$this->_redirect('checkout/onepage/success', array('_secure' => true));
	}

	protected function _saveOffer($offer)
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		$quote->savePayLaterOffer($offer);
	}

	protected function _getPayLaterOfferArray()
	{
		$quote = Mage::getModel('paylater/checkout_quote');
		return $quote->getPayLaterOfferArray();
	}

	protected function _verifyGatewayPost($post)
	{
		$helper = Mage::helper('paylater');
		foreach ($this->_requiredGatewayPostKeys as $key) {
			if (!array_key_exists($key, $post)) {
				$helper->log('PayLater gateway post key missing: ' . $key, __METHOD__);
				return false;
			}
			if (!$post[$key] || strlen(trim($post[$key])) == 0) {
				$helper->log('PayLater gateway post key-value not set: ' . $key, __METHOD__);
				return false;
			}
		}
		return true;
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
		if (!$this->_verifyGatewayPost($paylaterData)) {
			$this->_redirectGatewayError($helper->__(self::PAYLATER_GATEWAY_MISSING_DATA_ERROR));
			return;
		}
		try {
			// stops sending order email for order in saveOrder
			if ($onepage->saveOrder()) {
				try {
					if ($helper->getPayLaterConfigGatewayDomainVerification('globals')) {
						if (!$helper->canAccessGateway()) {
							$session = Mage::getSingleton('checkout/session')->addError($helper->__(self::PAYLATER_GATEWAY_WRONG_WAY_ERROR));
							$this->_setPayLaterOrderStateAndStatus(self::PAYLATER_FAILED_ORDER_STATE, self::PAYLATER_FAILED_ORDER_STATUS);
							$this->_redirectGatewayError('Invalid domain detected');
							return;
						}
					}
					// Order was saved without sending any customer email.
					// @see Observer->saveOrderAfter
					$orderId = $this->_setPayLaterOrderStateAndStatus(self::PAYLATER_ORPHANED_ORDER_STATUS, self::PAYLATER_ORPHANED_ORDER_STATUS);

					try {
						// Preserve checkout session ID incase user never comes back to merchant site with successful application
						// Used in cronjob
						Mage::getModel('sales/order')->loadByIncrementId($orderId)->setPaylaterAdditional(serialize(
										array(
											'checkout_session_id' => Mage::getSingleton('checkout/session')->getSessionId()
										)
								))->save();
					} catch (Exception $e) {
						$helper->log("Unable to save session to order '$orderId'", __METHOD__, Zend_Log::ERR);
					}
					$this->_setOrderPayLaterOffer($orderId);
					$paylaterData[self::PAYLATER_PARAMS_MAP_ORDERID_KEY] = $orderId;
					if ($helper->isEndpointAvailable(60)) {
						$this->getResponse()->setHeader('Cache-Control', 'no-cache, no-store');
						$this->getResponse()->setHeader('Pragma', 'no-cache');
						$this->getResponse()->setHeader('Expires', '0');
						$this->loadLayout();
						$this->getLayout()->getBlock('head')->setTitle($this->__(self::PAYLATER_GATEWAY_TITLE));
						$this->renderLayout();
						$helper->log('Saved order with id ' . $orderId, __METHOD__);
					} else {
						$helper->log($helper->__(self::PAYLATER_GATEWAY_CONNECT_ERROR), __METHOD__, Zend_Log::ERR);
						$this->_redirectGatewayError(self::PAYLATER_GATEWAY_CONNECT_ERROR);
						return;
					}
				} catch (PayLater_PayLater_Exception_InvalidHttpClientResponse $e) {
					/**
					 * @deprecated catch 
					 * 
					 * Nowhere is thrown a PayLater_PayLater_Exception_InvalidHttpClientResponse
					 */
					$helper->log($e->getMessage(), __METHOD__, Zend_Log::ERR);
					$this->_redirectGatewayError($e->getMessage());
					return;
				} catch (Mage_Core_Exception $e) {
					$helper->log($e->getMessage(), __METHOD__, Zend_Log::ERR);
					$this->_redirectGatewayError($e->getMessage());
					return;
				} catch (Exception $e) {
					$this->_redirectGatewayError($e->getMessage());
					return;
				}
			}
		} catch (Mage_Core_Exception $e) {
			$helper->log($e->getMessage(), __METHOD__, Zend_Log::ERR);
			$this->_redirectGatewayError($e->getMessage());
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

					// Poll PayLater for X seconds expecting a response with status
					$future = Mage::getModel('core/date')->timestamp(time()) + self::PAYLATER_POLLING_TIMEOUT;
					$count = 1;
					while (Mage::getModel('core/date')->timestamp(time()) <= $future) {
						$helper->log("Polling... $count", __METHOD__, Zend_Log::DEBUG);
						$apiResponse = Mage::getModel('paylater/api_response', array($apiRequest));
						$helper->log("\tResponse: " . $apiResponse->getStatus(), __METHOD__, Zend_Log::DEBUG);

						// Break as soon as we have a useful status
						if ($apiResponse->isSuccessful() && $apiResponse->hasStatus()) {
							break;
						}
						sleep(self::PAYLATER_POLLING_INTERVAL);
						$count++;
					}

					if ($apiResponse->isSuccessful() && $apiResponse->getStatus() == self::PAYLATER_API_ACCEPTED_RESPONSE && $apiResponse->doesAmountMatch($order)) {
						$order->setStateAndStatus();
						$order->savePayLaterOrderStatus($apiResponse->getStatus());
						$order->save();
						if ($order->invoice()) {
							$order->sendEmail();
						}
						$order->setInactiveQuote();
						Mage::dispatchEvent('paylater_response', array('response' => $apiResponse->getStatus(), 'orderId' => $orderId));
						$this->_toOnepageSuccess();
						return;
					} else {
						$order->savePayLaterOrderStatus($apiResponse->getStatus());
						$order->setStateAndStatus($apiResponse->getStatus());
						$order->save();
						Mage::dispatchEvent('paylater_response', array('response' => $apiResponse->getStatus(), 'orderId' => $orderId));
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

	public function saveOfferAction()
	{
		if ($this->getRequest()->isPost()) {
			$offer = $this->getRequest()->getPost();
			$this->_saveOffer($offer);
			$this->getResponse()->setBody(self::PAYLATER_SAVE_OFFER_SUCCESS);
			return;
		}
		$this->getResponse()->setBody(self::PAYLATER_SAVE_OFFER_FAILURE);
	}

}