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
class PayLater_PayLater_Adminhtml_Config_UpdateController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * @todo add logs for exceptions  
	 */
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
			
		} catch (PayLater_PayLater_Exception_ServiceUnavailable $e) {
			$session->addError(Mage::helper('paylater')->__('PayLater Configuration could not be updated. Service Unavailable.'));
		}catch (Exception $e) {
			$session->addError(Mage::helper('paylater')->__('PayLater Configuration could not be updated. ' . $e->getMessage()));
		}
	}
}