<?php

/**
 * PayLater extension for Magento
 *
 * Long description of this file (if any...)
 *
 * NOTICE OF LICENSE
 *
 * [TO BE DEFINED]
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Wonga PayLater module to newer versions in the future.
 * If you wish to customize the PayLater module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   PayLater
 * @package    PayLater_PayLater
 * @copyright  Copyright (C) 2012 PayLater
 * @license    [TO BE DEFINED]
 */

/**
 *
 * Static methods provider
 * 
 * @category   PayLater
 * @package    PayLater_PayLater
 * @subpackage Helper
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class PayLater_PayLater_Module implements PayLater_PayLater_Event_DispatchableInterface
{

	private function __construct()
	{
	}

	public static function dispatchEvent($handle, array $parameters)
	{
		if (is_array($parameters) && count($parameters) > 0) {
			isset($handle) ? Mage::dispatchEvent($handle, $parameters) : NULL;
		} else {
			isset($handle) ? Mage::dispatchEvent($handle) : NULL;
		}
	}

}
