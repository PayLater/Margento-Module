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
class PayLater_PayLater_Model_Checkout_Quote implements PayLater_PayLater_Core_Interface, PayLater_PayLater_Core_RangeableInterface
{
	protected function _getInSession ()
	{
		return Mage::getSingleton('checkout/session')->getQuote();
	}
	
	protected function _getGrandTotal ()
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
	
	protected function _getAllItems ()
	{
		return $this->_getInSession()->getAllItems();
	}
	
	protected function _getReservedOrderId()
	{
		return $this->_getInSession()->getReservedOrderId();
	}
	
	protected function _getBilling()
	{
		return $this->_getInSession()->getBillingAddress();
	}
	
	protected function _getShipping()
	{
		return $this->_getInSession()->getShippingAddress();
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
	
}
