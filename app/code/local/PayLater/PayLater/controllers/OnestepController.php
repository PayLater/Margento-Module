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
require_once 'Idev/OneStepCheckout/controllers/AjaxController.php';

/**
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @subpackage Block
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class PayLater_PayLater_OnestepController extends Idev_OneStepCheckout_AjaxController
{

	public function reviewAction()
	{
		$helper = Mage::helper('onestepcheckout/checkout');

		$shipping_method = $this->getRequest()->getPost('shipping_method');
		$old_shipping_method = $this->_getOnepage()->getQuote()->getShippingAddress()->getShippingMethod();

		if ($shipping_method != '' && $shipping_method != $old_shipping_method) {
			// Use our helper instead
			$helper->saveShippingMethod($shipping_method);
		}

		$paymentMethod = $this->getRequest()->getPost('payment_method', false);
		$selectedMethod = $this->_getOnepage()->getQuote()->getPayment()->getMethod();

		$store = $this->_getOnepage()->getQuote() ? $this->_getOnepage()->getQuote()->getStoreId() : null;
		$methods = $helper->getActiveStoreMethods($store, $this->_getOnepage()->getQuote());

		if ($paymentMethod && !empty($methods) && !in_array($paymentMethod, $methods)) {
			$paymentMethod = false;
		}

		if (!$paymentMethod && $selectedMethod && in_array($selectedMethod, $methods)) {
			$paymentMethod = $selectedMethod;
		}

		try {
			$payment = $this->getRequest()->getPost('payment', array());
			
			if (!empty($paymentMethod)) {
				$payment['method'] = $paymentMethod;
			}
			$helper->savePayment($payment);
		} catch (Exception $e) {

		}
		$this->_getOnepage()->getQuote()->collectTotals()->save();
		$this->loadLayout(false);
		$this->renderLayout();
	}

	public function billingAction()
	{
		$helper = Mage::helper('onestepcheckout/checkout');

		$billing_data = $this->getRequest()->getPost('billing', array());
		$shipping_data = $this->getRequest()->getPost('shipping', array());
		$customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
		$shippingAddressId = $this->getRequest()->getPost('shipping_address_id', false);

		$billing_data = $helper->load_exclude_data($billing_data);
		$shipping_data = $helper->load_exclude_data($shipping_data);

		if (Mage::helper('customer')->isLoggedIn() && $helper->differentShippingAvailable()) {
			if (!empty($customerAddressId)) {
				$billingAddress = Mage::getModel('customer/address')->load($customerAddressId);
				if (is_object($billingAddress) && $billingAddress->getCustomerId() == Mage::helper('customer')->getCustomer()->getId()) {
					$billing_data = array_merge($billing_data, $billingAddress->getData());
				}
			}
			if (!empty($shippingAddressId)) {
				$shippingAddress = Mage::getModel('customer/address')->load($shippingAddressId);
				if (is_object($shippingAddress) && $shippingAddress->getCustomerId() == Mage::helper('customer')->getCustomer()->getId()) {
					$shipping_data = array_merge($shipping_data, $shippingAddress->getData());
				}
			}
		}

		if (!empty($billing_data['use_for_shipping'])) {
			$shipping_data = $billing_data;
		}

		// set customer tax/vat number for further usage
		if (!empty($billing_data['taxvat'])) {
			$this->_getOnepage()->getQuote()->setCustomerTaxvat($billing_data['taxvat']);
			$this->_getOnepage()->getQuote()->setTaxvat($billing_data['taxvat']);
			$this->_getOnepage()->getQuote()->getBillingAddress()->setTaxvat($billing_data['taxvat']);
		} else {
			$this->_getOnepage()->getQuote()->setCustomerTaxvat('');
			$this->_getOnepage()->getQuote()->setTaxvat('');
			$this->_getOnepage()->getQuote()->getBillingAddress()->setTaxvat('');
		}

		$this->_getOnepage()->getQuote()->getBillingAddress()->addData($billing_data)->implodeStreetAddress()->setCollectShippingRates(true);
		$paymentMethod = $this->getRequest()->getPost('payment_method', false);
		$selectedMethod = $this->_getOnepage()->getQuote()->getPayment()->getMethod();

		$store = $this->_getOnepage()->getQuote() ? $this->_getOnepage()->getQuote()->getStoreId() : null;
		$methods = $helper->getActiveStoreMethods($store, $this->_getOnepage()->getQuote());

		if ($paymentMethod && !empty($methods) && !in_array($paymentMethod, $methods)) {
			$paymentMethod = false;
		}

		if (!$paymentMethod && $selectedMethod && in_array($selectedMethod, $methods)) {
			$paymentMethod = $selectedMethod;
		}

		if ($this->_getOnepage()->getQuote()->isVirtual()) {
			$this->_getOnepage()->getQuote()->getBillingAddress()->setPaymentMethod(!empty($paymentMethod) ? $paymentMethod : null);
		} else {
			$this->_getOnepage()->getQuote()->getShippingAddress()->setPaymentMethod(!empty($paymentMethod) ? $paymentMethod : null);
		}

		try {
			if ($paymentMethod) {
				$this->_getOnepage()->getQuote()->getPayment()->getMethodInstance();
			}
		} catch (Exception $e) {
			
		}

		$result = $this->_getOnepage()->saveBilling($billing_data, $customerAddressId);

		if (Mage::helper('customer')->isLoggedIn()) {
			$this->_getOnepage()->getQuote()->getBillingAddress()->setSaveInAddressBook(empty($billing_data['save_in_address_book']) ? 0 : 1);
			$this->_getOnepage()->getQuote()->getShippingAddress()->setSaveInAddressBook(empty($shipping_data['save_in_address_book']) ? 0 : 1);
		}

		if ($helper->differentShippingAvailable()) {
			if (empty($billing_data['use_for_shipping'])) {
				$shipping_result = $helper->saveShipping($shipping_data, $shippingAddressId);
			} else {
				$shipping_result = $helper->saveShipping($billing_data, $customerAddressId);
			}
		}

		$shipping_method = $this->getRequest()->getPost('shipping_method', false);

		if (!empty($shipping_method)) {
			$helper->saveShippingMethod($shipping_method);
		}

		$this->_getOnepage()->getQuote()->setTotalsCollectedFlag(false)->collectTotals();

		$this->loadLayout(false);

		if (Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('customer')->isLoggedIn()) {

			$customerBalanceBlock = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance', array('template' => 'onestepcheckout/customerbalance/payment/additional.phtml'));
			$customerBalanceBlockScripts = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance_scripts', array('template' => 'onestepcheckout/customerbalance/payment/scripts.phtml'));

			$rewardPointsBlock = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.points', array('template' => 'onestepcheckout/reward/payment/additional.phtml', 'before' => '-'));
			$rewardPointsBlockScripts = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.scripts', array('template' => 'onestepcheckout/reward/payment/scripts.phtml', 'after' => '-'));

			$this->getLayout()->getBlock('choose-payment-method')
					->append($customerBalanceBlock)
					->append($customerBalanceBlockScripts)
					->append($rewardPointsBlock)
					->append($rewardPointsBlockScripts)
			;
		}

		if (Mage::helper('onestepcheckout')->isEnterprise()) {
			$giftcardScripts = $this->getLayout()->createBlock('enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcardaccount_scripts', array('template' => 'onestepcheckout/giftcardaccount/onepage/payment/scripts.phtml'));
			$this->getLayout()->getBlock('choose-payment-method')
					->append($giftcardScripts);
		}

		$this->renderLayout();
	}

}