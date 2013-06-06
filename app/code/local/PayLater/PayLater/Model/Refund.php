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
class PayLater_PayLater_Model_Refund extends Mage_Core_Model_Abstract implements PayLater_PayLater_Core_Interface
{
	/**
     * Iterate collection and call callback method per item
     * For callback method first argument always is item object
     *
     * @param string $callback
     * @param array $args additional arguments for callback method
     * @param string $type unexported | all
     */
    protected function _exportIterateCollection($callback, array $args, $type)
    {
		if ($type == "unexported") {
			$originalCollection = $this->getUnexportedCollection();
		} else if ($type == 'all') {
			$originalCollection = $this->getAllRefundsCollection();
		} else {
			// fallbak: if type is not known or have a typo, get all refunds collection
			$originalCollection = $this->getAllRefundsCollection();
		}
		
        $count = null;
        $page  = 1;
        $lPage = null;
        $break = false;

        while ($break !== true) {
            $collection = clone $originalCollection;
            $collection->setPageSize(self::PAYLATER_REFUNDS_PAGE_SIZE);
            $collection->setCurPage($page);
            $collection->load();
            if (is_null($count)) {
                $count = $collection->getSize();
                $lPage = $collection->getLastPageNumber();
            }
            if ($lPage == $page) {
                $break = true;
            }
            $page ++;

            foreach ($collection as $item) {
                call_user_func_array(array($this, $callback), array_merge(array($item), $args));
            }
        }
    }
	
	/**
     * Write item data to csv export file
     *
     * @param Varien_Object $item
     * @param Varien_Io_File $adapter
     */
    protected function _exportCsvItem(Varien_Object $item, Varien_Io_File $adapter)
    {
        $row = $item->getData();
		if (is_array($row) && array_key_exists('refund_id', $row) && array_key_exists('export_date', $row)) {
			unset($row['refund_id']);
			unset($row['export_date']);
		}
        $adapter->streamWriteCsv($row);
		$now = Mage::getModel('core/date')->timestamp(time());
		$item->setData('export_date', date('Y-m-d H:i:s', $now));
		$item->save();
    }
	
	/**
	 *
	 * @param type $type
	 * @return boolean|PayLater_PayLater_Helper_Data
	 */
	protected function _helper($type = "data")
	{
		if ($type) {
			return ($type != "data") ? Mage::helper('paylater/' . $type) : Mage::helper('paylater');
		}
		return FALSE;
	}
	
	/**
	 * 
	 * @param array $data
	 * @return Mage_Sales_Model_Order
	 */
	protected function _getOrderFromRequest($data)
	{
		if (!isset($data['order_id']) || (isset($data['order_id']) && !$data['order_id'])) {
			Mage::throwException("Order ID is not set, unable to create PayLater refund.");
		}
		$order = Mage::getModel('sales/order')->load($data['order_id']);
		if (!$order->getId()) {
			Mage::throwException("Order ID '{$data['order_id']}' is invalid, unable to create PayLater refund.");
		}
		return $order;
	}

	/**
	 * 
	 * @param array $data
	 * @return string
	 */
	protected function _getReasonCodeFromRequest($data)
	{
		if (!isset($data['creditmemo']) || (isset($data['creditmemo']) && empty($data['creditmemo']))) {
			Mage::throwException("Request data does not contain item 'creditmemo', is this a refund request?");
		}
		if (!isset($data['creditmemo']['reason_for_refund']) || (isset($data['creditmemo']['reason_for_refund']) && !$data['creditmemo']['reason_for_refund'])) {
			Mage::throwException("Request data does not contain a 'Reason For Refund', is this a refund request?");
		}
		$reason_code = $data['creditmemo']['reason_for_refund'];
		if (!in_array($reason_code, explode(PayLater_PayLater_Core_Interface::REASON_CODES_SEPARATOR, PayLater_PayLater_Core_Interface::REASON_CODES))) {
			Mage::throwException("Reason code '{$reason_code}' does not exist, cannot create PayLater refund");
		}
		return $reason_code;
	}

	/**
	 * 
	 * @param Mage_Sales_Model_Order $order
	 * @return string
	 */
	protected function _getISOCurrencyCode(Mage_Sales_Model_Order $order)
	{
		$currency = $order->getOrderCurrencyCode();
		$currency_const = "CURRENCY_" . $currency . "_ISO";
		$currency_code = constant('PayLater_PayLater_Core_Interface::' . $currency_const);
		if (!$currency_code) {
			Mage::throwException("Error getting ISO code for '{$currency}', cannot create PayLater refund");
		}
		return $currency_code;
	}
	
	public function _construct()
	{
		$this->_init('paylater/refund');
	}

	
	
    /**
	 * 
     * Retrieve a file container array as CSV
     *
     * Return array with keys type and value
	 * 
	 * @param string $type (unexported|all)
	 * @return array
	 */
    public function getCsvFile($type)
    {
        $io = new Varien_Io_File();
        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.csv';

        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);
        $io->streamWriteCsv($this->getCsvRefundHeaders());
		
		$this->_exportIterateCollection('_exportCsvItem', array($io), $type);

        $io->streamUnlock();
        $io->streamClose();

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true // can delete file after use
        );
    }
	
	public function getCsvExportFilename ($type)
	{
		$now = Mage::getModel('core/date')->timestamp(time());
		return "PayLaterRefund_" . ucfirst($type) . '_' . date('Y-m-d_H-i-s', $now) . ".csv";
	}

	/**
	 * Generate a CSV of unexported refund records
	 * 
	 * @return string|boolean
	 * @deprecated since version 1.3.2
	 */
	public function generateRefundsCsv()
	{
		$records = $this->getResourceCollection()->getUnexportedRecords();
		$this->_helper()->log("Starting CSV Export of " . count($records) . " refunds", __METHOD__);
		$csv = new Varien_File_Csv();
		$tmp_file = Mage::getBaseDir() . "/var/cache/PayLaterRefund_" . date('Y-m-d_H-i-s') . ".csv";
		$rows = array();

		// Adding header row
		$header = array();
		$header['MerchantReference'] = 'MerchantReference';
		$header['OrderID'] = 'OrderID';
		$header['Currency'] = 'Currency';
		$header['RefundValue'] = 'RefundValue';
		$header['ReasonCode'] = 'ReasonCode';
		$header['RefundDate'] = 'RefundDate';
		$rows[] = $header;


		foreach ($records as $record) {
			$row = array();
			$record->load($record->getId());

			$this->_helper()->log("Exporting refund " . $record->getId(), __METHOD__, Zend_Log::DEBUG);
			$row['RefundDate'] = $record->getData('refund_date');
			$row['MerchantReference'] = $record->getData('merchant_reference');
			$row['OrderID'] = $record->getData('order_id');
			$row['Currency'] = $record->getData('currency');
			$row['RefundValue'] = $record->getData('refund_value');
			$row['ReasonCode'] = $record->getData('reason_code');
			$rows[] = $row;
		}
		if ($csv->saveData($tmp_file, $rows)) {
			$records->walk('markAsExported');
			$this->_helper()->log("Finished CSV export to " . $tmp_file, __METHOD__);
			return $tmp_file;
		}

		return FALSE;
	}
	
	/**
	 * Generate a CSV of unexported refund records
	 * @deprecated since version 1.3.2
	 * @return string|boolean
	 */
	public function generateAllRefundsCsv()
	{
		$records = $this->getResourceCollection();
		$this->_helper()->log("Starting CSV Export of " . count($records) . " refunds", __METHOD__);
		//$csv = new Varien_File_Csv();
		$tmpfile = "PayLaterAllRefunds_" . date('Y-m-d_H-i-s') . ".csv";
		$rows = array();

		// Adding header row
		$header = array();
		$header['RefundDate'] = 'RefundDate';
		$header['MerchantReference'] = 'MerchantReference';
		$header['OrderID'] = 'OrderID';
		$header['Currency'] = 'Currency';
		$header['RefundValue'] = 'RefundValue';
		$header['ReasonCode'] = 'ReasonCode';
		$rows[] = $header;


		foreach ($records as $record) {
			$row = array();
			$record->load($record->getId());

			$this->_helper()->log("Exporting refund " . $record->getId(), __METHOD__, Zend_Log::DEBUG);
			$row['RefundDate'] = $record->getData('refund_date');
			$row['MerchantReference'] = $record->getData('merchant_reference');
			$row['OrderID'] = $record->getData('order_id');
			$row['Currency'] = $record->getData('currency');
			$row['RefundValue'] = $record->getData('refund_value');
			$row['ReasonCode'] = $record->getData('reason_code');
			$rows[] = $row;
		}
		
		if (count($rows) > 0) {
			return array ('filename' => $tmpfile, 'headers' => $rows);
		}

		return FALSE;
	}
	
	/**
	 * Returns CSV export headers array
	 * 
	 * @return array
	 */
	public function getCsvRefundHeaders ()
	{
		$headers = array();
		$headers[] = 'MerchantReference';
		$headers[] = 'OrderID';
		$headers[] = 'Currency';
		$headers[] = 'RefundValue';
		$headers[] = 'ReasonCode';
		$headers[] = 'RefundDate';
		return $headers;
	}
	
	public function getAllRefundsCollection ()
	{
		return $this->getResourceCollection();
	}
	
	public function getUnexportedCollection ()
	{
		return $this->getResourceCollection()->getUnexportedRecords();
	}

	/**
	 * Mark record as exported with current date
	 * 
	 * @deprecated since version 1.3.2 logic now in _exportCsvItem
	 * @return bool
	 */
	public function markAsExported()
	{
		$this->setData('export_date', date('Y-m-d H:i:s'));
		if ($this->save()) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * 
	 * @return bool
	 */
	public function hasRecordsToExport()
	{
		return $this->getResourceCollection()->hasRecordsToExport();
	}

	/**
	 * Create database record for creditmemo
	 * 
	 * @return bool
	 */
	public function createRefundRecord($amount)
	{
		$data = Mage::app()->getRequest()->getParams();
		$order = $this->_getOrderFromRequest($data);
		$reason_code = $this->_getReasonCodeFromRequest($data);
		$merchant_reference = $this->_helper()->getPayLaterConfigReference('merchant');
		if (!$merchant_reference) {
			Mage::throwException("Merchant reference is not set, unable to create PayLater refund.");
		}
		$currency_code = $this->_getISOCurrencyCode($order);

		$this->setData('merchant_reference', $merchant_reference);
		$this->setData('order_id', $order->getIncrementId());
		$this->setData('currency', $currency_code);
		$this->setData('refund_value', $amount);
		$this->setData('reason_code', $reason_code);

		if ($this->save()) {
			$this->_helper()->log("Created Refund record '{$this->getId()}' for Order '{$order->getIncrementId()}'", __METHOD__);
			return TRUE;
		}

		return FALSE;
	}
}