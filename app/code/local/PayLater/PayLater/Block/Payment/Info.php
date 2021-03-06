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
class PayLater_PayLater_Block_Payment_Info extends Mage_Payment_Block_Info implements PayLater_PayLater_Core_Interface, PayLater_PayLater_Core_ShowableInterface
{

	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate(self::PAYMENT_METHOD_INFO_TEMPLATE);
	}

	/**
	 * Returns order model in scope in sales_order_edit layout block,
	 * or false otherwise.
	 * 
	 * Catches exceptions for graceful layout load
	 * 
	 * @return Mage_Sales_Model_Order
	 */
	protected function _getOrder()
	{
		try {
			$_orderInfo = Mage::helper('paylater/layout')->getCoreLayout()->getBlock('order_info');
			if ($_orderInfo) {
				return $_orderInfo->getOrder();
			}

			return false;
		} catch (Mage_Exception $e) {
			return false;
		} catch (Exception $e) {
			return false;
		}
		return false;
	}

	public function getPayLaterLogoSrc()
	{
		return $this->getSkinUrl(self::PAYLATER_LOGO_SRC);
	}

	/**
	 * 
	 * @return Mage_Sales_Model_Order
	 */
	public function getOrder()
	{
		return $this->_getOrder();
	}

	/**
	 * @see PayLater_PayLater_Core_ShowableInterface
	 * @return boolean 
	 */
	public function canShow()
	{
		$helper = Mage::helper('paylater');
		return $helper->canShowAtCheckout() && $helper->isAllowedCurrency();
	}

}
