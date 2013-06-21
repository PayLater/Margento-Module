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
 * Mark a class as able provide PayLater type (i.e. product or checkout)
 * 
 * @category   PayLater
 * @package    PayLater_PayLater
 * @subpackage Model
 * @author     GPMD <dev@gpmd.co.uk>
 */
interface PayLater_PayLater_Core_WidgetInterface
{
    public function hasWidgetBeenConfigured();
	public function getConfiguredWidgetJSON();
	public function getConfiguredWidget();
	public function getWidgetName();
	public function doesWidgetHaveBreakdown ();
	public function doesWidgetHaveParameters ();
	public function getWidgetShowBreakdown();
	public function getWidgetParamsAsString();
	public function getAmount();
}