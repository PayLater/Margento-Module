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
 * Core interface for PayLater module.
 *  
 * This interface MUST define only constants.
 * 
 * @category   PayLater
 * @package    PayLater_PayLater
 * @subpackage Model
 * @author     GPMD <dev@gpmd.co.uk>
 */
interface PayLater_PayLater_Core_Interface
{
	/**
	 * XML_NODE_CDN
	 * 
	 * @deprecated
	 */

	const XML_NODE_CDN = 'paylater/static/cdn';

	/**
	 * System config developer log active node 
	 */
	const XML_NODE_SYSTEM_DEV_LOG_ACTIVE = 'dev/log/active';
	/**
	 * Templates 
	 */
	const SYSTEM_CONFIG_INFO_TEMPLATE = 'paylater/paylater/system/config/fieldset/info.phtml';
	const SERVICE_UNAVAILABLE_TEMPLATE = 'paylater/paylater/service/unavailable.phtml';
	const PAYMENT_METHOD_FORM_TEMPLATE = 'paylater/paylater/method/form.phtml';
	const PAYMENT_METHOD_INFO_TEMPLATE = 'paylater/paylater/method/info.phtml';
	const BASKET_NOT_IN_RANGE_TEMPLATE = 'paylater/paylater/checkout/basket/notinrange.phtml';
	const CHECKOUT_GRANDTOTAL_TEMPLATE = 'paylater/paylater/tax/checkout/grandtotal.phtml';
	const PRICE_JS_BLOCK = 'Mage_Core_Block_Template';
	const PRICE_JS_BLOCK_NAME = 'paylater.pricejs';
	const PRICE_JS_TEMPLATE = 'paylater/paylater/pricejs.phtml';
	const PAYLATER_LOGO_SRC = 'paylater/images/paylater_logo_small.png';

	/**
	 * ENV Types 
	 */
	const ENVIRONMENT_TEST = 'test';
	const ENVIRONMENT_LIVE = 'live';
	/**
	 * PayLater Endpoints  
	 */
	const PAYLATER_ENDPOINT = 'https://orders.paylater.com';
	const PAYLATER_ENDPOINT_SERVER = 'orders.paylater.com';
	const PAYLATER_ENDPOINT_SERVER_PORT = 443;
	const PAYLATER_ENDPOINT_TEST = 'http://staging.orders.paylater.wongatest.com';
	const PAYLATER_ENDPOINT_TEST_SERVER = 'staging.orders.paylater.wongatest.com';
	const PAYLATER_ENDPOINT_TEST_SERVER_PORT = 80;
	/**
	 * PayLater API 
	 */
	const PAYLATER_XMLNS = "http://wonga.com/api/3.0";
	const PAYLATER_XMLNS_XSI = "http://www.w3.org/2001/XMLSchema-instance";
	const PAYLATER_API_QUERIES_TEST = 'http://staging.merchantapi.paylater.wongatest.com/queries';
	const PAYLATER_API_QUERIES_LIVE = 'https://merchantapi.paylater.com/queries';
	const PAYLATER_API_COMMANDS_TEST = 'http://staging.merchantapi.paylater.wongatest.com/commands';
	const PAYLATER_API_COMMANDS_LIVE = 'https://merchantapi.paylater.com/commands';
	const PAYLATER_API_REQUEST_MAXREDIRECTS = 0;
	const PAYLATER_API_REQUEST_TIMEOUT = 30;
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
	const PAYLATER_PRICE_JS_TEST = 'https://paylater.s3.amazonaws.com/price.js';
	/**
	 * Supported PayLater currencies.
	 */
	const CURRENCY_SEPARATOR = ',';
	const PAYLATER_CURRENCIES = 'GBP';
	const CURRENCY_GBP_ISO = 826;

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
	const SERVICE_TIMEOUT = 5;
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
	const PAYLATER_ONESTEP_BEFORE_ENDPOINT_ACTION = 'paylater/onestep/gateway';
	/**
	 * PayLater return link 
	 */
	const PAYLATER_POST_RETURN_ERROR_LINK = 'checkout/onepage';
	const PAYLATER_ONESTEP_POST_RETURN_ERROR_LINK = 'onestepcheckout/';
	/**
	 * Orders states and statuses 
	 */
	const PAYLATER_ORPHANED_ORDER_STATUS = 'paylater_orphaned';
	const PAYLATER_ORPHANED_ORDER_STATE = 'paylater_orphaned';
	const PAYLATER_FAILED_ORDER_STATUS = 'paylater_failed';
	const PAYLATER_FAILED_ORDER_STATE = 'paylater_failed';
	/**
	 * PayLater endpoint post keys and values 
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
	const ERROR_CODE_GENERIC = 600;
	/**
	 * PayLater response 
	 */
	const PAYLATER_SUMMARY_RESPONSE_NODE = 'GetOrderSummaryResponse';
	const PAYLATER_SUMMARY_RESPONSE_STATUS_NODE = 'Status';
	const PAYLATER_SUMMARY_RESPONSE_AMOUNT_NODE = 'Amount';
	const PAYLATER_SUMMARY_RESPONSE_POSTCODE_NODE = 'PostCode';
	const PAYLATER_API_ACCEPTED_RESPONSE = 'accepted';
	/**
	 * New order email templates 
	 */
	const XML_PATH_PAYLATER_EMAIL_TEMPLATE = 'paylater/order/template';
	const XML_PATH_PAYLATER_EMAIL_TEMPLATE_NODE = 'paylater_order_template';
	const XML_PATH_PAYLATER_EMAIL_GUEST_TEMPLATE = 'paylater/order/guest_template';
	const XML_PATH_PAYLATER_EMAIL_GUEST_TEMPLATE_NODE = 'paylater_order_guest_template';
	/**
	 * PayLater offer  keys 
	 */
	const PAYLATER_SAVE_OFFER_PATH = 'paylater/checkout/saveOffer';
	const PAYLATER_INFO_TEXT = 'paylater_info_text';
	const PAYLATER_EMAIL_INFO_TEXT = 'paylater_email_info_text';
	const PAYLATER_ORDER_STATUS = 'paylater_order_status';
	const PAYLATER_AMOUNT = 'paylater_amount';
	const PAYLATER_FEE_PRICE = 'paylater_fee_price';
	const PAYLATER_AGREEMENT_DURATION_DAYS = 'paylater_duration_days';
	const PAYLATER_INSTALLMENTS_AMOUNT = 'paylater_installments_amount';
	const PAYLATER_APR = 'paylater_apr';
	const PAYLATER_TOTAL_TO_BE_PAID = 'paylater_total_to_be_paid';
	const PAYLATER_ADDITIONAL = 'paylater_additional';
	const PAYLATER_SAVE_OFFER_SUCCESS = 'offer-ok';
	const PAYLATER_SAVE_OFFER_FAILURE = 'offer-ko';

	/**
	 * Refund reason codes
	 */
	const REASON_CODES_SEPARATOR = ',';
	const REASON_CODES = 'MR01,MR02,MR03,MR04,MR05,MR06,MR07,MR08,MR09,MR10,MR11,MR12,MR13,MR14';
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

	/**
	 *  PayLater Checkout Messages
	 */
	const PAYLATER_CHECKOUT_ONEPAGE_MESSAGES_BLOCK = 'PayLater_PayLater_Block_Checkout_Onepage_Messages';
	/**
	 * Checkout type
	 * 
	 */
	const PAYLATER_CHECKOUT_TYPE_ONEPAGE = 'onepage';
	const PAYLATER_CHECKOUT_TYPE_ONESTEP = 'onestep';

}
