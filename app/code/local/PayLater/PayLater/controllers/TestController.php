<?php

class PayLater_PayLater_TestController extends Mage_Core_Controller_Front_Action
{
	public function serviceAvailableAction()
    {
		var_dump(Mage::helper('paylater')->isServiceAvailable());
    }
	
	public function hasCacheExpiredAction()
    {
		$cacheFactory = Mage::getModel('paylater/cache_factory');
		var_dump(Mage::helper('paylater')->hasCacheExpired($cacheFactory));
    }
	
	public function saveCacheDataAction()
    {
		$cacheFactory = Mage::getModel('paylater/cache_factory');
		try {
			var_dump(Mage::helper('paylater')->loadCacheData($cacheFactory));
		} catch (PayLater_PayLater_Exception_ServiceUnavailable $e) {
			echo $e->getMessage();
		}
		var_dump(Mage::helper('paylater')->hasCacheExpired($cacheFactory));
    }
	
	public function merchantCdnAction()
	{
		$cdn = Mage::helper('paylater')->getMerchantServiceCdn();
		$client = new Zend_Http_Client($cdn);
		var_dump(json_decode($client->request()->getBody(), true));
	}
}