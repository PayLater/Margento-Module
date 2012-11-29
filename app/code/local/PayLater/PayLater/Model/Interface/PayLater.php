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
interface PayLater_PayLater_Model_Interface_PayLater
{
	const XML_NODE_SYSTEM_DEV_LOG_ACTIVE = 'dev/log/active';
	const SYSTEM_CONFIG_INFO_TEMPLATE = 'paylater/paylater/system/config/fieldset/info.phtml';
	const XML_NODE_CDN = 'paylater/static/cdn';
	const ENVIRONMENT_TEST = 'test';
	const ENVIRONMENT_LIVE = 'live';
}
