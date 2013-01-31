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
class PayLater_PayLater_Model_Mysql4_Refund_Collection extends Mage_Sales_Model_Mysql4_Collection_Abstract
{

	public function _construct()
	{
		$this->_init('paylater/refund');
	}

	/**
	 * Determines if there are any records that have not been exported
	 * 
	 * @return boolean
	 */
	public function hasRecordsToExport()
	{
		$record_collection = $this->getUnexportedRecords();
		if (count($record_collection)) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Returns a collection of unexported records
	 * 
	 * @return PayLater_PayLater_Model_Mysql4_Refund_Collection
	 */
	public function getUnexportedRecords()
	{
		$this->addFieldToFilter('export_date', array('null' => 1));
		return $this;
	}
	
	public function exportAllRecords ()
	{
		return $this;
	}

}