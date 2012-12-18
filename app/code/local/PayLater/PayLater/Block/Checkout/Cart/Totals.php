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
 * @subpackage Block
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class PayLater_PayLater_Block_Checkout_Cart_Totals extends Mage_Checkout_Block_Cart_Totals implements PayLater_PayLater_Core_Interface
{

	protected function _getTotalRenderer($code)
	{

		$blockName = $code . '_total_renderer';
		$block = $this->getLayout()->getBlock($blockName);

		if ($this->getQuote()->getPayment()->getMethod() == self::PAYLATER_PAYMENT_METHOD && $code == 'grand_total') {
			if (!$block) {
				$block = 'paylater/checkout_grandtotal';
				$block = $this->getLayout()->createBlock($block, $blockName);
				$block->setTotals($this->getTotals());
				return $block;
			}
		} else {
			if (!$block) {
				$block = $this->_defaultRenderer;
				$config = Mage::getConfig()->getNode("global/sales/quote/totals/{$code}/renderer");
				if ($config) {
					$block = (string) $config;
				}

				$block = $this->getLayout()->createBlock($block, $blockName);
			}
			/**
			 * Transfer totals to renderer
			 */
			$block->setTotals($this->getTotals());
			return $block;
		}
	}
}
