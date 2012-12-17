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
 * @subpackage Model
 * @author     GPMD Ltd <dev@gpmd.co.uk>
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
			$invoiceId = Mage::getModel('sales/order_invoice_api')->create($this->_getInstance()->getIncrementId(), array());
			$invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceId);
			$invoice->capture()->save();
			return true;
		}
		return false;
	}

	/**
	 * Send email with PayLater order data
	 *
	 * @return Mage_Sales_Model_Order
	 */
	public function _sendNewOrderEmail()
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
			'paylater_info' => '<p>You paid xx</p>'
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

	public function getInstance()
	{
		return $this->_getInstance();
	}

	public function setStateAndStatus()
	{
		$this->_getInstance()->setState(Mage_Sales_Model_Order::STATE_NEW);
		$this->_getInstance()->setStatus(Mage::helper('paylater')->getPayLaterConfigOrderStatus('payment'));
	}

	public function save()
	{
		$this->_getInstance()->save();
	}

	public function canInvoice()
	{
		return $this->_getInstance()->canInvoice();
	}

	public function invoice()
	{
		return $this->_invoice();
	}

	public function sendEmail()
	{
		return $this->_sendNewOrderEmail();
	}
	
	public function setInactiveQuote ()
	{
		Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
	}

}
