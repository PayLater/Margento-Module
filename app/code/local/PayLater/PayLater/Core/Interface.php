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
interface PayLater_PayLater_Core_Interface
{
	const XML_NODE_SYSTEM_DEV_LOG_ACTIVE = 'dev/log/active';
	const SYSTEM_CONFIG_INFO_TEMPLATE = 'paylater/paylater/system/config/fieldset/info.phtml';
	const XML_NODE_CDN = 'paylater/static/cdn';
	const ENVIRONMENT_TEST = 'test';
	const ENVIRONMENT_LIVE = 'live';
	const SERVICE_HOSTNAME = 's3-eu-west-1.amazonaws.com';
	const MERCHANTS_CDN = 'https://s3-eu-west-1.amazonaws.com/paylater/merchants/%s/config.json';
	const PAYLATER_PRICE_JS = 'https://paylater.s3.amazonaws.com/price.js';
	const SERVICE_PORT = 443;
	const SERVICE_TIMEOUT = 3;
	const FEE_PERCENT_KEY = 'FeePercent';
	const ORDER_LOWER_BOUND = 'OrderLowerBound';
	const ORDER_UPPER_BOUND = 'OrderUpperBound';
	const PAYLATER_TYPE_PRODUCT = 'product';
	const PAYLATER_TYPE_CHECKOUT = 'checkout';
	const PAYLATER_PAYMENT_METHOD = 'paylater';
	const PAYLATER_ENDPOINT_TEST = 'http://staging.orders.paylater.wongatest.com';
	const PAYLATER_BEFORE_ENDPOINT_ACTION = 'paylater/checkout/saveOrder';
	const PAYLATER_ORPHANED_ORDER_STATUS = 'PayLater Orphaned';
	const PAYLATER_ORPHANED_ORDER_STATE = 'PayLater Orphaned';
}
