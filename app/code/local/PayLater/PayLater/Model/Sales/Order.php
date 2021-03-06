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
class PayLater_PayLater_Model_Sales_Order implements PayLater_PayLater_Core_Interface
{

	protected $_order;

	protected function _getInstance()
	{
		return $this->_order;
	}

	protected function _getEmails($configPath)
	{
		$data = Mage::getStoreConfig($configPath, $this->_getInstance()->getStoreId());
		if (!empty($data)) {
			return explode(',', $data);
		}
		return false;
	}

	/**
	 * Creates an invoice in 'Pending' state initially and then changes it
	 * to 'Paid', and return true.
	 *
	 * False otherwise.
	 *
	 * @return bool
	 */
	protected function _invoice()
	{
		if ($this->_getInstance()->canInvoice()) {
			$order = $this->_getInstance();
			$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
			$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
			$invoice->register();
			$transaction = Mage::getModel('core/resource_transaction')
					->addObject($invoice)
					->addObject($invoice->getOrder());
			$transaction->save();
			return true;
		}
		return false;
	}

	/**
	 * Send email with PayLater order data
	 *
	 * @return Mage_Sales_Model_Order
	 */
	protected function _sendNewOrderEmail()
	{
		$storeId = $this->_getInstance()->getStore()->getId();

		if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
			return $this;
		}
		// Get the destination email addresses to send copies to
		$copyTo = $this->_getEmails(Mage_Sales_Model_Order::XML_PATH_EMAIL_COPY_TO);
		$copyMethod = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_COPY_METHOD, $storeId);
		// Start store emulation process
		$appEmulation = Mage::getSingleton('core/app_emulation');
		$initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

		try {
			// Retrieve specified view block from appropriate design package (depends on emulated store)
			$paymentBlock = Mage::helper('payment')->getInfoBlock($this->_getInstance()->getPayment())
					->setIsSecureMode(true);
			$paymentBlock->getMethod()->setStore($storeId);
			$paymentBlockHtml = $paymentBlock->toHtml();
		} catch (Exception $exception) {
			// Stop store emulation process
			$appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
			throw $exception;
		}

		// Stop store emulation process
		$appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

		// Retrieve corresponding email template id and customer name
		if ($this->_getInstance()->getCustomerIsGuest()) {
			$templateId = Mage::getStoreConfig(self::XML_PATH_PAYLATER_EMAIL_GUEST_TEMPLATE, $storeId);
			$customerName = $this->_getInstance()->getBillingAddress()->getName();
		} else {
			$templateId = Mage::getStoreConfig(self::XML_PATH_PAYLATER_EMAIL_TEMPLATE, $storeId);
			$customerName = $this->_getInstance()->getCustomerName();
		}

		$mailer = Mage::getModel('core/email_template_mailer');
		$emailInfo = Mage::getModel('core/email_info');
		$emailInfo->addTo($this->_getInstance()->getCustomerEmail(), $customerName);
		$this->_getInstance()->getCustomerEmail();
		if ($copyTo && $copyMethod == 'bcc') {
			// Add bcc to customer email
			foreach ($copyTo as $email) {
				$emailInfo->addBcc($email);
			}
		}
		$mailer->addEmailInfo($emailInfo);

		// Email copies are sent as separated emails if their copy method is 'copy'
		if ($copyTo && $copyMethod == 'copy') {
			foreach ($copyTo as $email) {
				$emailInfo = Mage::getModel('core/email_info');
				$emailInfo->addTo($email);
				$mailer->addEmailInfo($emailInfo);
			}
		}

		// Set all required params and send emails
		$mailer->setSender(Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId));
		$mailer->setStoreId($storeId);
		$mailer->setTemplateId($templateId);
		$mailer->setTemplateParams(array(
			'order' => $this->_getInstance(),
			'billing' => $this->_getInstance()->getBillingAddress(),
			'payment_html' => $paymentBlockHtml,
			'paylater_info' => $this->_getInstance()->getPaylaterEmailInfoText()
				)
		);
		$mailer->send();

		$this->_getInstance()->setEmailSent(true);
		$this->_getInstance()->getResource()->saveAttribute($this->_getInstance(), 'email_sent');

		return $this->_getInstance();
	}

	public function __construct(array $arguments)
	{
		if (!array_key_exists(0, $arguments) && !(is_numeric($arguments[0]))) {
			throw new PayLater_PayLater_Exception_InvalidArguments(__METHOD__ . ' ' . Mage::helper('paylater')->__('Invalid arguments. First argument must be a valid Magento order increment id'));
		}
		$this->_order = Mage::getModel('sales/order')->load($arguments[0], 'increment_id');
	}

	/**
	 * 
	 * @return Mage_Sales_Model_Order
	 */
	public function getInstance()
	{
		return $this->_getInstance();
	}
	
	public function setStateAndStatus($paylaterStatus = self::PAYLATER_API_ACCEPTED_RESPONSE)
	{
		switch ($paylaterStatus) {
			case self::PAYLATER_API_ACCEPTED_RESPONSE:
				$this->setNewStateAndStatus();
				break;
			
			case self::PAYLATER_API_DECLINED_RESPONSE:
				$this->setDeclinedStateAndStatus();
				break;
			
			case self::PAYLATER_API_CANCELED_RESPONSE:
				$this->setCanceledStateAndStatus();
				break;
			
			case self::PAYLATER_API_PENDING_RESPONSE:
				// Leave in orphaned state
				break;
			
			case '':
				// If no reply leave in orphaned state
				break;
			
			default:
				Mage::helper('paylater')->log("Unknown PayLater response '$paylaterStatus'", __METHOD__, Zend_Log::ERR);
				$this->setFailedStateAndStatus();
				break;
		}
	}

	/**
	 *  Sets new order state and status
	 */
	public function setNewStateAndStatus()
	{
		$this->_getInstance()->setState(Mage_Sales_Model_Order::STATE_NEW);
		$this->_getInstance()->setStatus(Mage::helper('paylater')->getPayLaterConfigOrderStatus('payment'));
	}
	
	public function setCanceledStateAndStatus()
	{
		$this->_getInstance()->setState(Mage_Sales_Model_Order::STATE_CANCELED);
		$this->_getInstance()->setStatus(Mage_Sales_Model_Order::STATE_CANCELED);
	}
        
	public function setFailedStateAndStatus()
	{
		$this->_getInstance()->setState(self::PAYLATER_FAILED_ORDER_STATE);
		$this->_getInstance()->setStatus(self::PAYLATER_FAILED_ORDER_STATUS);
	}

	public function setDeclinedStateAndStatus()
	{
		$this->_getInstance()->setState(Mage_Sales_Model_Order::STATE_CANCELED);
		$this->_getInstance()->setStatus(self::PAYLATER_DECLINED_ORDER_STATUS);
	}

	/**
	 * Order save method wrapper
	 * 
	 */
	public function save()
	{
		$this->_getInstance()->save();
	}

	public function canInvoice()
	{
		return $this->_getInstance()->canInvoice();
	}

	/**
	 * Programmatically invoice order
	 * 
	 * @return bool
	 */
	public function invoice()
	{
		return $this->_invoice();
	}

	/**
	 *  Sends new order email
	 */
	public function sendEmail()
	{
		$this->_sendNewOrderEmail();
	}

	/**
	 * Gets checkout session quote and set it to inactive
	 */
	public function setInactiveQuote()
	{
		Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
	}

	/**
	 * Saves PayLaterOffer quote fields to order
	 * 
	 * @param array $offer 
	 */
	public function savePayLaterOffer(array $offer)
	{
		$this->_getInstance()->setPaylaterInfoText($offer[self::PAYLATER_INFO_TEXT]);
		$this->_getInstance()->setPaylaterEmailInfoText($offer[self::PAYLATER_EMAIL_INFO_TEXT]);
		$this->_getInstance()->setPaylaterAmount($offer[self::PAYLATER_AMOUNT]);
		$this->_getInstance()->setPaylaterFeePrice($offer[self::PAYLATER_FEE_PRICE]);
		$this->_getInstance()->setPaylaterInstallmentsAmount($offer[self::PAYLATER_INSTALLMENTS_AMOUNT]);
		$this->_getInstance()->setPaylaterDurationDays($offer[self::PAYLATER_AGREEMENT_DURATION_DAYS]);
		$this->_getInstance()->setPaylaterApr($offer[self::PAYLATER_APR]);
		$this->_getInstance()->setPaylaterTotalToBePaid($offer[self::PAYLATER_TOTAL_TO_BE_PAID]);
		$this->save();
	}

	public function savePayLaterOrderStatus($status)
	{
		$this->_getInstance()->setPaylaterOrderStatus($status);
	}

}
