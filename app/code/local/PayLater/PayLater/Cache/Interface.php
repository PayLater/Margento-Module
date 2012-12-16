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
 * @subpackage Model
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */
interface PayLater_PayLater_Cache_Interface
{

	const FRONTEND_TTL = 3600;
	const FRONTEND_AUTO_SERIALIZE = true;
	const BACKEND_CACHE_DIR = '/tmp';
	const BACKEND_FILE_PREFIX = 'paylater';
	const CID_FORMAT = 'paylater_%s';

	/**
	 *
	 * @return Zend_Cache_Core 
	 */
	public function getInstance();

	public function getFrontendOptions();

	public function getBackendOptions();

	public function getId();
	
	public function hasExpired();
	
	public function save ();
}