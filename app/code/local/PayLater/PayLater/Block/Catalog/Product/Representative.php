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
class PayLater_PayLater_Block_Catalog_Product_Representative extends Mage_Core_Block_Template implements PayLater_PayLater_Core_Interface, PayLater_PayLater_Core_ShowableInterface, PayLater_PayLater_Core_TypeableInterface
{

	/**
	 * @see PayLater_PayLater_Core_ShowableInterface
	 * @return boolean 
	 */
	public function canShow()
	{
		$helper = Mage::helper('paylater');
		return $helper->canShowOnProduct() && $helper->isAllowedCurrency();
	}

	public function getProductPrice()
	{
		$currentProduct = Mage::registry('product');
		return $currentProduct->getPrice();
	}
	
	public function getProductFinalPrice()
	{
		$currentProduct = Mage::registry('product');
		return $currentProduct->getFinalPrice();
	}
	
	public function isConfigurableProduct()
	{
		$currentProduct = Mage::registry('product');
		return $currentProduct->isConfigurable();
	}

	/**
	 * @see PayLater_PayLater_Core_TypeableInterface
	 * @return string 
	 */
	public function getPayLaterType()
	{
		return self::PAYLATER_TYPE_PRODUCT;
	}

	/**
	 * Returns event type as defined in system config
	 * @return string|null 
	 */
	public function getEventType()
	{
		return Mage::helper('paylater')->getPayLaterConfigEvent('product');
	}

}
