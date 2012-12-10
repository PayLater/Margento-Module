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
 * Core interface for PayLater module.
 *  
 * This interface MUST define only constants.
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
	/**
	 * XML_NODE_CDN
	 * 
	 * @deprecated 
	 */
	const XML_NODE_CDN = 'paylater/static/cdn';
	/**
	 * ENV Types 
	 */
	const ENVIRONMENT_TEST = 'test';
	const ENVIRONMENT_LIVE = 'live';
	/**
	 * PayLater Endpoints  
	 */
	const PAYLATER_ENDPOINT = 'http://staging.orders.paylater.wongatest.com';
	const PAYLATER_ENDPOINT_TEST = 'http://staging.orders.paylater.wongatest.com';
	/**
	 *  SERVICE_HOSTNAME_: where we get config.json
	 * 
	 * @see PayLater_PayLater_Helper_Data->isServiceAvailable method
	 */
	const SERVICE_HOSTNAME_LIVE = 's3-eu-west-1.amazonaws.com';
	const SERVICE_HOSTNAME_TEST = 's3-eu-west-1.amazonaws.com';
	/**
	 * MERCHANT_CDN where we get config.json for a particular merchant 
	 * @see PayLater_PayLater_Helper_Data->getMerchantServiceCdn method
	 */
	const MERCHANTS_CDN = 'https://s3-eu-west-1.amazonaws.com/paylater/merchants/%s/config.json';
	/**
	 * PAYLATER_PRICE_JS  
	 */
	const PAYLATER_PRICE_JS = 'https://paylater.s3.amazonaws.com/price.js';
	const PAYLATER_PRICE_JS_TEST = 'http://staging.paylater.wongatest.com/paylater-merchant/price.js';
	const SERVICE_PORT_TEST = 80;
	const SERVICE_PORT = 443;
	const SERVICE_TIMEOUT = 3;
	const FEE_PERCENT_KEY = 'FeePercent';
	const ORDER_LOWER_BOUND = 'OrderLowerBound';
	const ORDER_UPPER_BOUND = 'OrderUpperBound';
	const PAYLATER_CACHE_HASHED_DIRECTORY_LEVEL = 1;
	const PAYLATER_CACHE_HASHED_DIRECTORY_UMASK = 0777;
	const PAYLATER_TYPE_PRODUCT = 'product';
	const PAYLATER_TYPE_CHECKOUT = 'checkout';
	const PAYLATER_PAYMENT_METHOD = 'paylater';
	const PAYLATER_BEFORE_ENDPOINT_ACTION = 'paylater/checkout/gateway';
	const PAYLATER_POST_RETURN_ERROR_LINK = 'checkout/onepage';
	const PAYLATER_ORPHANED_ORDER_STATUS = 'PayLater Orphaned';
	const PAYLATER_ORPHANED_ORDER_STATE = 'PayLater Orphaned';
	const PAYLATER_PARAMS_MAP_REFERENCE_KEY = 'reference';
	const PAYLATER_PARAMS_MAP_RETURN_LINK_KEY = 'returnlink';
	const PAYLATER_PARAMS_MAP_RETURN_LINK = 'paylater/order/processPayLaterResponse';
	const PAYLATER_PARAMS_MAP_AMOUNT_KEY = 'amount';
	const PAYLATER_PARAMS_MAP_ORDERID_KEY = 'merchantorderid';
	const PAYLATER_PARAMS_MAP_CURRENCY_KEY = 'currency';
	const PAYLATER_PARAMS_MAP_POSTCODE_KEY = 'postcode';
	const PAYLATER_PARAMS_MAP_ITEMS_KEY = 'items';
	const PAYLATER_PARAMS_MAP_ITEM_ID_KEY = 'id';
	const PAYLATER_PARAMS_MAP_ITEM_ID_DESCRIPTION_KEY = 'description';
	const PAYLATER_PARAMS_MAP_ITEM_ID_QTY_KEY = 'quantity';
	const PAYLATER_PARAMS_MAP_ITEM_ID_PRICE_KEY = 'price';
}
