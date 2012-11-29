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
 * @category   PayLater
 * @package    PayLater_PayLater
 * @subpackage Helper
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
class PayLater_PayLater_Helper_Inflector extends Mage_Core_Helper_Data
{
	/**
	 * Convert string in format 'StringString' to 'string_string'
	 *
	 * @param  string $string  string to underscore
	 * @return string $string  underscored string
	 */
	public function underscore($string) {
		return str_replace(' ', '_', strtolower ( preg_replace ( '~(?<=\\w)([A-Z])~', '_$1', $string ) ));
	}
}
