<?php

class PayLater_PayLater_TestController extends Mage_Core_Controller_Front_Action
{
	function serviceAvailableAction()
    {
		var_dump(Mage::helper('paylater')->isServiceAvailable());
    }
	
	function hasCacheExpiredAction()
    {
		$cacheFactory = Mage::getModel('paylater/factory_cache');
		var_dump(Mage::helper('paylater')->hasCacheExpired($cacheFactory));
    }
	
	function saveCacheDataAction()
    {
		$cacheFactory = Mage::getModel('paylater/factory_cache');
		try {
			var_dump(Mage::helper('paylater')->saveCacheData($cacheFactory));
		} catch (PayLater_PayLater_Exception_ServiceUnavailable $e) {
			echo $e->getMessage();
		}
		var_dump(Mage::helper('paylater')->hasCacheExpired($cacheFactory));
    }
}