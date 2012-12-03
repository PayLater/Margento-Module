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
class PayLater_PayLater_Model_Event_Observer implements PayLater_PayLater_Core_Interface
{
	public function catalogProductViewBefore(Varien_Event_Observer $observer) 
	{
		$payLater = Mage::helper('paylater');
		$isEnabled = $payLater->getPayLaterConfigRunStatus('globals');
		
		if ($isEnabled) {
			$cache = Mage::getModel('paylater/cache_factory');
			$payLaterData = $payLater->loadCacheData($cache);
			if (is_array($payLaterData)) {
				$currentProduct = Mage::getModel('paylater/catalog_product');
				if ($currentProduct->isWithinPayLaterRange($payLaterData)){
					$layout = Mage::helper('paylater/layout');
					$layout->setPriceJs();
				}
			}
		}
	}
	
	public function checkoutOnepageIndexBefore(Varien_Event_Observer $observer)
	{
		$payLater = Mage::helper('paylater');
		$isEnabled = $payLater->getPayLaterConfigRunStatus('globals');
		
		if ($isEnabled) {
			$cache = Mage::getModel('paylater/cache_factory');
			$payLaterData = $payLater->loadCacheData($cache);
			if (is_array($payLaterData)) {
				$quote = Mage::getModel('paylater/checkout_quote');
				if ($quote->isWithinPayLaterRange($payLaterData)){
					$layout = Mage::helper('paylater/layout');
					$layout->setPriceJs();
				}
			}
		}
	}
	
	public function coreBlockToHtmlBefore(Varien_Event_Observer $observer)
	{
		if($observer->getBlock()->getType() == "checkout/onepage_review_info") {
			$onepage = Mage::getModel('paylater/checkout_onepage');
			$chosenMethod = $onepage->getPaymentMethod();
			if ($chosenMethod == self::PAYLATER_PAYMENT_METHOD) {
				$info = $observer->getBlock();
				//$info->setType('paylater/onepage_review_info');
				$info->setTemplate ('paylater/paylater/checkout/review/info.phtml');
				$totals = $info->getChild('totals');
				//$totals->setType('paylater/cart_totals');
				$totals->setTemplate('paylater/paylater/checkout/review/totals.phtml');
				$button = $info->getChild('button');
				$button->setTemplate('');
			}
		}
	}
}
