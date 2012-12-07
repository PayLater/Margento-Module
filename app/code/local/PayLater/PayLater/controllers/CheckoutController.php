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

	protected function _getOnepage()
	{
		return Mage::getModel('paylater/checkout_onepage')->getSingleton();
	}
	
	protected function _collectAllItems ()
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

	protected function _postToPayLater($data)
	{
		$env = Mage::helper('paylater')->getPayLaterConfigEnv('globals');
		if ($env == self::ENVIRONMENT_TEST) {
			$client = new Varien_Http_Client(self::PAYLATER_ENDPOINT_TEST);
		}
		/**
		 *@todo LIVE ENV 
		 */
		$this->_collectAllItems();
		$client->setMethod(Varien_Http_Client::POST);
		$client->setParameterPost(self::PAYLATER_PARAMS_MAP_REFERENCE_KEY, $data[self::PAYLATER_PARAMS_MAP_REFERENCE_KEY]);
		$client->setParameterPost(self::PAYLATER_PARAMS_MAP_AMOUNT_KEY, $data[self::PAYLATER_PARAMS_MAP_AMOUNT_KEY]);
		$client->setParameterPost(self::PAYLATER_PARAMS_MAP_ORDERID_KEY, $data[self::PAYLATER_PARAMS_MAP_ORDERID_KEY]);
		$client->setParameterPost(self::PAYLATER_PARAMS_MAP_CURRENCY_KEY, $data[self::PAYLATER_PARAMS_MAP_CURRENCY_KEY]);
		$client->setParameterPost(self::PAYLATER_PARAMS_MAP_POSTCODE_KEY, $data[self::PAYLATER_PARAMS_MAP_POSTCODE_KEY]);
		$client->setParameterPost(self::PAYLATER_PARAMS_MAP_RETURN_LINK_KEY,  Mage::getBaseUrl() . self::PAYLATER_PARAMS_MAP_RETURN_LINK);
		$client->setParameterPost('item',  $this->_collectAllItems());
		try {
			$response = $client->request();
			if ($response->isSuccessful()) {
				echo $response->getBody();
				//$xml = new SimpleXMLElement($response->getBody());
				//echo $client->getLastRequest();
				//echo $response->getHeadersAsString();
			} else {
				//echo $response->getBody();
			}
		} catch (Exception $e) {
			throw new PayLater_PayLater_Exception_InvalidHttpClientResponse('InvalidHttpClientResponse: ' . $e->getMessage());
		}
	}
	/**
	 * Saves order and sets its status and state to PayLater Orphaned 
	 */
	public function saveOrderAction()
	{
		$onepage = $this->_getOnepage();
		$quote = $onepage->getQuote();
		$quote->collectTotals()->save();
		$paylaterData = $this->getRequest()->getPost();
		try {
			if ($onepage->saveOrder()) {
				try {
					$orderId = $quote->getReservedOrderId();
					$order = Mage::getModel('paylater/checkout_onepage')->getOrder($orderId);
					$order->setState(self::PAYLATER_ORPHANED_ORDER_STATE);
					$order->setStatus(self::PAYLATER_ORPHANED_ORDER_STATUS);
					$order->save();
					$paylaterData[self::PAYLATER_PARAMS_MAP_ORDERID_KEY] = $orderId;
					$this->_postToPayLater($paylaterData);
				} catch (PayLater_PayLater_Exception_InvalidHttpClientResponse $e) {
					echo $e->getMessage();
				} catch (Mage_Core_Exception $e) {
					echo $e->getMessage();
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}
		} catch (Mage_Core_Exception $e) {
			echo $e->getMessage();
		}
	}
}