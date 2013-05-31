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
class PayLater_PayLater_Model_Checkout_Quote implements PayLater_PayLater_Core_Interface, PayLater_PayLater_Core_RangeableInterface
{

	protected function _getInSession()
	{
		return Mage::getSingleton('checkout/session')->getQuote();
	}

	protected function _getGrandTotal()
	{
		return $this->_getInSession()->getGrandTotal();
	}

	protected function _getBillingPostcode()
	{
		return $this->_getInSession()->getBillingAddress()->getPostcode();
	}

	protected function _getShippingPostcode()
	{
		return $this->_getInSession()->getShippingAddress()->getPostcode();
	}

	protected function _getAllItems()
	{
		return $this->_getInSession()->getAllItems();
	}

	protected function _getReservedOrderId()
	{
		return $this->_getInSession()->getReservedOrderId();
	}
	
	protected function _getBillingEmail()
	{
		return $this->_getInSession()->getBillingAddress()->getEmail();
	}
	
	protected function _getBillingFirstname()
	{
		return $this->_getInSession()->getBillingAddress()->getFirstname();
	}
	
	protected function _getBillingLastname()
	{
		return $this->_getInSession()->getBillingAddress()->getLastname();
	}
	
	protected function _getBillingPhone()
	{
		return $this->_getInSession()->getBillingAddress()->getTelephone();
	}

	protected function _getBilling()
	{
		return $this->_getInSession()->getBillingAddress();
	}

	protected function _getShipping()
	{
		return $this->_getInSession()->getShippingAddress();
	}

	protected function _isVirtual()
	{
		return $this->_getInSession()->isVirtual();
	}

	public function getInstance()
	{
		return $this->_getInSession();
	}

	/**
	 * @see PayLater_PayLater_Core_RangeableInterface
	 * @param array $paylaterData
	 * @return bool 
	 */
	public function isWithinPayLaterRange($paylaterData)
	{
		$grandTotal = $this->_getGrandTotal();
		$orderLowerBound = $paylaterData[self::ORDER_LOWER_BOUND];
		$orderUpperBound = $paylaterData[self::ORDER_UPPER_BOUND];
		return $grandTotal >= $orderLowerBound && $grandTotal <= $orderUpperBound;
	}

	/**
	 * Returns quote grand total
	 * @return float 
	 */
	public function getGrandTotal()
	{
		return $this->_getGrandTotal();
	}

	/**
	 * Returns billing address postcode
	 * 
	 * @return string/bool 
	 */
	public function getBillingPostcode()
	{
		return $this->_getBillingPostcode();
	}

	/**
	 * Returns shipping address postcode
	 * 
	 * @return string/bool 
	 */
	public function getShippingPostcode()
	{
		return $this->_getShippingPostcode();
	}

	/**
	 * Retrieve quote items array
	 *
	 * @return array
	 */
	public function getAllItems()
	{
		return $this->_getAllItems();
	}

	/**
	 * Returns quote reserved order id, or FALSE otherwise
	 * 
	 * @return int|bool 
	 */
	public function getReservedOrderId()
	{
		return $this->_getReservedOrderId();
	}

	/**
	 * Gets quote billing address
	 * 
	 * @return type 
	 */
	public function getBillingAddress()
	{
		return $this->_getBilling();
	}

	/**
	 * Gets quote shipping address
	 * 
	 * @return type 
	 */
	public function getShippingAddress()
	{
		return $this->_getShipping();
	}
	
	/**
	 * Gets customer billing email address
	 * 
	 * @return string
	 */
	public function getBillingCustomerEmail()
	{
		return $this->_getBillingEmail();
	}
	
	/**
	 * Gets customer billing firstname
	 * 
	 * @return string
	 */
	public function getBillingFirstname()
	{
		return $this->_getBillingFirstname();
	}
	
	/**
	 * Gets customer billing lastname
	 * 
	 * @return string
	 */
	public function getBillingLastname()
	{
		return $this->_getBillingLastname();
	}
	
	/**
	 * Gets customer billing phone
	 * 
	 * @return string
	 */
	public function getBillingPhone()
	{
		return $this->_getBillingPhone();
	}

	/**
	 * Gets postcode to be posted to PayLater.
	 * If quote is virtual returns billing postcode, 
	 * returns shipping postcode otherwise.
	 * 
	 * @return string|bool 
	 */
	public function getPayLaterPostcode()
	{
		if ($this->_isVirtual()) {
			return $this->getBillingPostcode();
		}

		return $this->getShippingPostcode();
	}

	/**
	 * Saves PayLater offer at review step
	 * 
	 * @param array $offer
	 */
	public function savePayLaterOffer(array $offer)
	{
		$this->_getInSession()->setPaylaterInfoText($offer[self::PAYLATER_INFO_TEXT]);
		$this->_getInSession()->setPaylaterEmailInfoText($offer[self::PAYLATER_EMAIL_INFO_TEXT]);
		$this->_getInSession()->setPaylaterAmount($offer[self::PAYLATER_AMOUNT]);
		$this->_getInSession()->setPaylaterFeePrice($offer[self::PAYLATER_FEE_PRICE]);
		$this->_getInSession()->setPaylaterInstallmentsAmount($offer[self::PAYLATER_INSTALLMENTS_AMOUNT]);
		$this->_getInSession()->setPaylaterDurationDays($offer[self::PAYLATER_AGREEMENT_DURATION_DAYS]);
		$this->_getInSession()->setPaylaterApr($offer[self::PAYLATER_APR] . '%');
		$this->_getInSession()->setPaylaterTotalToBePaid($offer[self::PAYLATER_TOTAL_TO_BE_PAID]);
		$this->_getInSession()->save();
	}

	/**
	 * Gets PayLater offer array
	 * 
	 * @return array
	 */
	public function getPayLaterOfferArray()
	{
		$offer = array(
			self::PAYLATER_INFO_TEXT => $this->_getInSession()->getPaylaterInfoText(),
			self::PAYLATER_EMAIL_INFO_TEXT => $this->_getInSession()->getPaylaterEmailInfoText(),
			self::PAYLATER_AMOUNT => $this->_getInSession()->getPaylaterAmount(),
			self::PAYLATER_FEE_PRICE => $this->_getInSession()->getPaylaterFeePrice(),
			self::PAYLATER_INSTALLMENTS_AMOUNT => $this->_getInSession()->getPaylaterInstallmentsAmount(),
			self::PAYLATER_AGREEMENT_DURATION_DAYS => $this->_getInSession()->getPaylaterDurationDays(),
			self::PAYLATER_APR => $this->_getInSession()->getPaylaterApr(),
			self::PAYLATER_TOTAL_TO_BE_PAID => $this->_getInSession()->getPaylaterTotalToBePaid()
		);
		return $offer;
	}

}
