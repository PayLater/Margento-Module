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
class PayLater_PayLater_Helper_Layout extends Mage_Core_Helper_Data implements PayLater_PayLater_Core_Interface
{

	/**
	 *
	 * @return Mage_Core_Model_Layout 
	 */
	protected function _getCoreLayout()
	{
		return Mage::getSingleton('core/layout');
	}

	/**
	 * Sets pricejs.phtml view in 'head' block
	 */
	public function setPriceJs()
	{
		$layout = $this->_getCoreLayout();
		$priceJs = $layout->createBlock(
				self::PRICE_JS_BLOCK, self::PRICE_JS_BLOCK_NAME, array('template' => self::PRICE_JS_TEMPLATE)
		);
		$headBlock = $layout->getBlock('head');
		$headBlock->append($priceJs);
	}

	/**
	 * Sets js.phtml view in 'head' block for onestep checkout
	 */
	public function setOnestepJs()
	{
		$layout = $this->_getCoreLayout();
		$priceJs = $layout->createBlock(
				self::PRICE_JS_BLOCK, 'paylater.onestep.js', array('template' => 'paylater/paylater/checkout/onestep/js.phtml')
		);
		$headBlock = $layout->getBlock('head');
		$headBlock->append($priceJs);
	}

	public function getCoreLayout()
	{
		return $this->_getCoreLayout();
	}

}
