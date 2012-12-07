<?php

class PayLater_PayLater_Adminhtml_Config_UpdateController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction()
	{
		$cache = Mage::getModel('paylater/cache_factory');
		$cache->getInstance()->clean(
			Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('PayLater')
		);
		
		try {
			$cache->save();
			$session = Mage::getSingleton('adminhtml/session');
			$session->addSuccess(Mage::helper('paylater')->__('PayLater Configuration has been successfully updated'));
			$this->_redirectReferer();

		} catch (PayLater_PayLater_Exception_InvalidMerchantData $e) {
			
		} catch (PayLater_PayLater_Exception_ServiceUnavailable $e) {
			
		}catch (Exception $e) {
			
		}
	}
}