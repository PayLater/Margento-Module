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
class PayLater_PayLater_OrderController extends Mage_Core_Controller_Front_Action implements PayLater_PayLater_Core_Interface
{
	
	public function processPayLaterResponseAction()
	{
		$session = Mage::getSingleton('customer/session');
		/**
		 * var PayLater_PayLater_Helper_Data 
		 */
		$helper = Mage::helper('paylater');
		$errorCode = $this->getRequest()->getParam('ErrorCodes');
		if ($errorCode && $errorCode > 0) {
			$session->addError($helper->__($helper->getPayLaterConfigErrorCodeBody('payment')));
			$helper->log($helper->getErrorMessageByCode(), __METHOD__, Zend_Log::ERR);
			$this->_redirect(self::PAYLATER_POST_RETURN_ERROR_LINK);
		}
	}
}