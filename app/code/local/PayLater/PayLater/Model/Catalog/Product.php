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
class PayLater_PayLater_Model_Catalog_Product implements PayLater_PayLater_Core_Interface, PayLater_PayLater_Core_RangeableInterface
{

	protected function _getInRegistry()
	{
		return Mage::registry('current_product');
	}

	protected function _getPrice()
	{
		return Mage::helper('tax')->getPrice($this->_getInRegistry(), $this->_getInRegistry()->getFinalPrice());
	}

	public function isWithinPayLaterRange($paylaterData)
	{
		$price = $this->_getPrice();
		$orderLowerBound = $paylaterData[self::ORDER_LOWER_BOUND];
		$orderUpperBound = $paylaterData[self::ORDER_UPPER_BOUND];
		return $price >= $orderLowerBound && $price <= $orderUpperBound;
	}

	public function getPrice()
	{
		return $this->_getPrice();
	}

}
