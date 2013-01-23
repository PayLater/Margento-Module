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
 * @copyright  Copyright (C) 2013 PayLater
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
class PayLater_PayLater_Adminhtml_Config_UpdateController extends Mage_Adminhtml_Controller_Action
{

	protected function _sendCsv($file_path)
	{
		if (!is_file($file_path) || !is_readable($file_path)) {
			Mage::throwException("PayLater CSV '$file_path' unavailable");
		}
		$this->getResponse()
				->setHttpResponseCode(200)
				->setHeader('Pragma', 'public', true)
				->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
				->setHeader('Content-type: text/csv')
				->setHeader('Content-Length', filesize($file_path))
				->setHeader('Content-Disposition', 'attachment' . '; filename=' . basename($file_path));
		$this->getResponse()->clearBody();
		$this->getResponse()->sendHeaders();
		readfile($file_path);
	}

	public function indexAction()
	{
		$session = Mage::getSingleton('adminhtml/session');
		$cache = Mage::getModel('paylater/cache_factory');
		$cache->getInstance()->clean(
				Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('PayLater')
		);

		try {
			$cache->save();
			$session->addSuccess(Mage::helper('paylater')->__('PayLater Configuration has been successfully updated'));
			$this->_redirectReferer();
		} catch (PayLater_PayLater_Exception_InvalidMerchantData $e) {
			$session->addError(Mage::helper('paylater')->__('PayLater Configuration could not be updated. Invalid Merchant Data.'));
			Mage::helper('paylater')->log(Mage::helper('paylater')->__($e->getMessage()), __METHOD__, Zend_Log::ERR);
		} catch (PayLater_PayLater_Exception_ServiceUnavailable $e) {
			$session->addError(Mage::helper('paylater')->__('PayLater Configuration could not be updated. Service Unavailable.'));
			Mage::helper('paylater')->log(Mage::helper('paylater')->__($e->getMessage()), __METHOD__, Zend_Log::ERR);
		} catch (Exception $e) {
			$session->addError(Mage::helper('paylater')->__('PayLater Configuration could not be updated. ' . $e->getMessage()));
			Mage::helper('paylater')->log(Mage::helper('paylater')->__($e->getMessage()), __METHOD__, Zend_Log::ERR);
		}
		$this->_redirectReferer();
	}

	public function refundAction()
	{
		$session = Mage::getSingleton('adminhtml/session');
		$file_path = Mage::getModel('paylater/refund')->generateRefundsCsv();
		try {
			$this->_sendCsv($file_path);
			unlink($file_path);
		} catch (Exception $e) {
			$session->addError(Mage::helper('paylater')->__($e->getMessage()));
			$this->_redirectReferer();
		}
	}

}