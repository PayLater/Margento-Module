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
		echo $cdn;
		var_dump(json_decode($client->request()->getBody(), true));
	}

	public function logTestAction()
	{
		$helper = Mage::helper('paylater')->getMerchantServiceCdn();
		var_dump('Can Helper log ' . $helper->canLog());
		$helper->log('Log test', __METHOD__);
	}

}