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
class PayLater_PayLater_Model_Source_RefundReason
{

	/**
	 * Get list of paylater refund reasons as array
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$options = array();
		foreach (explode(PayLater_PayLater_Core_Interface::ERROR_SEPARATOR, PayLater_PayLater_Core_Interface::REASON_CODES) as $code) {
			$reason_const = "REASON_{$code}_REASON";
			$option = array(
				'value' => $code,
				'label' => constant('PayLater_PayLater_Core_Interface::' . $reason_const),
			);
			array_push($options, $option);
		}

		return $options;
	}

}