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
class PayLater_PayLater_Block_Checkout_Onepage_Review_Info extends Mage_Checkout_Block_Onepage_Review_Info implements PayLater_PayLater_Core_Interface, PayLater_PayLater_Core_ShowableInterface
{

	public function _construct()
	{
		parent::_construct();
	}

	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		if (($this->canShow() === false || Mage::helper('paylater')->isEndpointAvailable() === false) && $this->helper('paylater')->isPayLaterPaymentMethod()) {
			$checkout = Mage::getModel('paylater/checkout_onepage')->getCheckout();
			$checkout->setStepData('review', 'allow', false);
			$checkout->setStepData('payment', 'allow', true);
			$this->setTemplate(self::SERVICE_UNAVAILABLE_TEMPLATE);
		}
		if (!$this->getTemplate()) {
			return '';
		}
		$html = $this->renderView();
		return $html;
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
