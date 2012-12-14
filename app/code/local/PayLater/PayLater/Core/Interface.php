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
	const CURRENCY_SEPARATOR = ',';
	const PAYLATER_CURRENCIES = 'GBP';
	/**
	 * Config events
	 */
	const PAYLATER_CLICK_EVENT = 'click';
	const PAYLATER_HOVER_EVENT = 'mouseover';
	/**
	 * Config nodes (defined in config.xml) 
	 */
	const PAYLATER_CONFIG_NODE_VERSION = 'modules/PayLater_PayLater/version';
	const PAYLATER_CONFIG_NODE_LICENSE_URL = 'paylater/license/url';
	const PAYLATER_CONFIG_NODE_SUPPORT_EMAIL = 'paylater/support/email';
	const PAYLATER_CONFIG_NODE_REPORT_EMAIL = 'paylater/report/bugs';
	const PAYLATER_CONFIG_NODE_DEV_EMAIL = 'paylater/dev/email';
	/**
	 * Test environment service port 
	 */
	const SERVICE_PORT_TEST = 80;
	/**
	 * Live environment service port 
	 */
	const SERVICE_PORT = 443;
	/**
	 * Live and Test environment service timeout
	 * 
	 * Note: this should be kept pretty low to avoid system pending when 
	 * service fsockopen return false
	 * 
	 * @see PayLater_PayLater_Helper_Data->isServiceAvailable method
	 *  
	 */
	const SERVICE_TIMEOUT = 3;
	/**
	 * config.json object keys 
	 */
	const FEE_PERCENT_KEY = 'FeePercent';
	const ORDER_LOWER_BOUND = 'OrderLowerBound';
	const ORDER_UPPER_BOUND = 'OrderUpperBound';
	/**
	 * PayLater cache config 
	 */
	const PAYLATER_CACHE_HASHED_DIRECTORY_LEVEL = 1;
	const PAYLATER_CACHE_HASHED_DIRECTORY_UMASK = 0777;
	/**
	 * PayLater types 
	 */
	const PAYLATER_TYPE_PRODUCT = 'product';
	const PAYLATER_TYPE_CHECKOUT = 'checkout';
	/**
	 * PayLater Payment method name 
	 */
	const PAYLATER_PAYMENT_METHOD = 'paylater';
	/**
	 * PayLater before endpoint post 
	 */
	const PAYLATER_BEFORE_ENDPOINT_ACTION = 'paylater/checkout/gateway';
	/**
	 * PayLater return link 
	 */
	const PAYLATER_POST_RETURN_ERROR_LINK = 'checkout/onepage';
	/**
	 * Orders states and statuses 
	 */
	const PAYLATER_ORPHANED_ORDER_STATUS = 'paylater_orphaned';
	const PAYLATER_ORPHANED_ORDER_STATE = 'paylater_orphaned';
	const PAYLATER_FAILED_ORDER_STATUS = 'paylater_failed';
	const PAYLATER_FAILED_ORDER_STATE = 'paylater_failed';
	/**
	 * PayLater endpoint post keys 
	 */
	const PAYLATER_PARAMS_MAP_REFERENCE_KEY = 'reference';
	const PAYLATER_PARAMS_MAP_RETURN_LINK_KEY = 'returnlink';
	const PAYLATER_PARAMS_MAP_RETURN_LINK = 'paylater/checkout/continue';
	const PAYLATER_PARAMS_MAP_AMOUNT_KEY = 'amount';
	const PAYLATER_PARAMS_MAP_ORDERID_KEY = 'merchantorderid';
	const PAYLATER_PARAMS_MAP_CURRENCY_KEY = 'currency';
	const PAYLATER_PARAMS_MAP_POSTCODE_KEY = 'postcode';
	const PAYLATER_PARAMS_MAP_ITEMS_KEY = 'items';
	const PAYLATER_PARAMS_MAP_ITEM_ID_KEY = 'id';
	const PAYLATER_PARAMS_MAP_ITEM_ID_DESCRIPTION_KEY = 'description';
	const PAYLATER_PARAMS_MAP_ITEM_MAX_DESCRIPTION_LENGTH = '2000';
	const PAYLATER_PARAMS_MAP_ITEM_ID_QTY_KEY = 'quantity';
	const PAYLATER_PARAMS_MAP_ITEM_ID_PRICE_KEY = 'price';
	/**
	 * Gateway page title 
	 */
	const PAYLATER_GATEWAY_TITLE = 'Contacting PayLater. Please wait...';
	//error codes
	const ERROR_SEPARATOR = ',';
	const ERROR_CODES = '101,102,103,104,105,106,107,108,109,110,211,212,213,214,215,216,217,218,219,322,432,433,434,435,436,500,501,600';
	const ERROR_101 = 'Empty parameters';
	const ERROR_102 = 'Empty amount passed';
	const ERROR_103 = 'Empty return link';
	const ERROR_104 = 'Empty order id passed';
	const ERROR_105 = 'Empty currency passed';
	const ERROR_106 = 'Empty/zero item values';
	const ERROR_107 = 'Item description is empty';
	const ERROR_108 = 'Item quantity is empty';
	const ERROR_109 = 'Item price is empty';
	const ERROR_110 = 'Item id is empty';
	const ERROR_211 = 'Reference is too long';
	const ERROR_212 = 'Amount is too long';
	const ERROR_213 = 'Return link is too long';
	const ERROR_214 = 'Order id is too long';
	const ERROR_215 = 'Postcode is too long';
	const ERROR_216 = 'Item id is too long';
	const ERROR_217 = 'Item quantity is too long';
	const ERROR_218 = 'Item price is too long';
	const ERROR_219 = 'Item description is too long';
	const ERROR_322 = 'Value passed is negative or zero';
	const ERROR_432 = 'Invalid amount passed';
	const ERROR_433 = 'Invalid returnlink passed';
	const ERROR_434 = 'Invalid currency passed';
	const ERROR_435 = 'Invalid item price passed';
	const ERROR_436 = 'Invalid item quantity passed';
	const ERROR_500 = 'Merchant does not exists';
	const ERROR_501 = 'Merchant order duplicated';
	const ERROR_600 = 'Generic error code for failed application';
	
	/**
	 * Refund reason codes
	 */
	const REASON_CODES = 'MR01,MR02,MR03,MR04,MR05,MR06,MR07,MR08,MR09,MR10,MR11,MR12,MR13,MR14,';
	const REASON_MR01_REASON = 'Partial Refund - Damaged Goods';
	const REASON_MR02_REASON = 'Partial Refund - Goods / Parts not Received';
	const REASON_MR03_REASON = 'Partial Refund - Good Not as Described';
	const REASON_MR04_REASON = 'Partial Refund - GOGW';
	const REASON_MR05_REASON = 'Partial Refund - Pricing change';
	const REASON_MR06_REASON = 'Partial Refund - Buyer Remorse';
	const REASON_MR07_REASON = 'Partial Refund - Other';
	const REASON_MR08_REASON = 'Full Refund - Damaged Goods';
	const REASON_MR09_REASON = 'Full Refund - Goods / Parts not Received';
	const REASON_MR10_REASON = 'Full Refund - Goods Not as Described';
	const REASON_MR11_REASON = 'Full Refund - GOGW';
	const REASON_MR12_REASON = 'Full Refund - Pricing change';
	const REASON_MR13_REASON = 'Full Refund - Buyer Remorse';
	const REASON_MR14_REASON = 'Full Refund - Other';
	
	const REASON_MR01_DESCRIPTION = 'Damaged items in order';
	const REASON_MR02_DESCRIPTION = 'Items missing from order';
	const REASON_MR03_DESCRIPTION = 'Customer doesn\'t get what they expected';
	const REASON_MR04_DESCRIPTION = 'Gesture of Goodwill from the merchant - can cover a multitude of internal merchant reasons for refund (poor service, "found cheaper elsewhere", etc)';
	const REASON_MR05_DESCRIPTION = 'Customer may have been charged an incorrect price at checkout';
	const REASON_MR06_DESCRIPTION = 'Customer changes mind, has second thoughts about order';
	const REASON_MR07_DESCRIPTION = 'Any other partial refund reason not captured by other refund codes';
	const REASON_MR08_DESCRIPTION = 'Damaged items in order';
	const REASON_MR09_DESCRIPTION = 'Items missing from order';
	const REASON_MR10_DESCRIPTION = 'Customer doesn\'t get what they expected';
	const REASON_MR11_DESCRIPTION = 'Gesture of Goodwill from the merchant - can cover a multitude of internal merchant reasons for refund (poor service, "found cheaper elsewhere", etc)';
	const REASON_MR12_DESCRIPTION = 'Customer may have been charged an incorrect price at checkout';
	const REASON_MR13_DESCRIPTION = 'Customer changes mind, has second thoughts about order';
	const REASON_MR14_DESCRIPTION = 'Any other full refund reason not captured by other refund codes';
}
