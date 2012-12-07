<?php

class PayLater_PayLater_CheckoutController extends Mage_Core_Controller_Front_Action implements PayLater_PayLater_Core_Interface
{

	protected function _postToPayLater($data)
	{
		$env = Mage::helper('paylater')->getPayLaterConfigEnv('globals');
		if ($env == self::ENVIRONMENT_TEST) {
			$client = new Varien_Http_Client(self::PAYLATER_ENDPOINT_TEST);
		}
		$client->setMethod(Varien_Http_Client::POST);
		$client->setParameterPost(self::PAYLATER_PARAMS_MAP_REFERENCE_KEY, $data[self::PAYLATER_PARAMS_MAP_REFERENCE_KEY]);
		$client->setParameterPost(self::PAYLATER_PARAMS_MAP_AMOUNT_KEY, $data[self::PAYLATER_PARAMS_MAP_AMOUNT_KEY]);
		$client->setParameterPost(self::PAYLATER_PARAMS_MAP_ORDERID_KEY, $data[self::PAYLATER_PARAMS_MAP_ORDERID_KEY]);
		$client->setParameterPost(self::PAYLATER_PARAMS_MAP_CURRENCY_KEY, $data[self::PAYLATER_PARAMS_MAP_CURRENCY_KEY]);
		$client->setParameterPost(self::PAYLATER_PARAMS_MAP_POSTCODE_KEY, $data[self::PAYLATER_PARAMS_MAP_POSTCODE_KEY]);
		$client->setParameterPost(self::PAYLATER_PARAMS_MAP_RETURN_LINK_KEY,  Mage::getBaseUrl() . self::PAYLATER_PARAMS_MAP_RETURN_LINK);
		
		try {
			$response = $client->request();
			if ($response->isSuccessful()) {
				echo $response->getBody();
				//$xml = new SimpleXMLElement($response->getBody());
				//echo $client->getLastRequest();
				//echo $response->getHeadersAsString();
			} else {
				echo $response->getBody();
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

	protected function _getOnepage()
	{
		return Mage::getModel('paylater/checkout_onepage')->getSingleton();
	}

}