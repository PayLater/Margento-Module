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
 * 
 * @category   PayLater
 * @package    PayLater_PayLater
 * @subpackage Block
 * @author     GPMD Ltd <dev@gpmd.co.uk>
 */

class PayLater_PayLater_Model_Refund extends Mage_Core_Model_Abstract
{
	
	public function _construct()
	{
		$this->_init('paylater/refund');
	}
	
	/**
	 *
	 * @param type $type
	 * @return boolean|PayLater_PayLater_Helper_Data
	 */
    protected function _helper($type = "data")
	{
        if($type){
			return ($type != "data") ? Mage::helper('paylater/'.$type) : Mage::helper('paylater');
		}
		return FALSE;
    }
	
	/**
	 * Generate a CSV of unexported refund records
	 * 
	 * @return string|boolean
	 */
	public function generateRefundsCsv()
	{
		$records = $this->getResourceCollection()->getUnexportedRecords();
		
		$this->_helper()->log("Starting CSV Export of ".count($records)." refunds", __CLASS__.'::'.__METHOD__);
		$csv = new Varien_File_Csv();
		$tmp_file = Mage::getBaseDir()."/var/cache/PayLaterRefund_".date('Y-m-d_H-i-s').".csv";
		$rows = array();
		
		$header = array();
		$header['RefundDate'] = 'RefundDate';
		$header['MerchantReference'] = 'MerchantReference';
		$header['OrderID'] = 'OrderID';
		$header['Currency'] = 'Currency';
		$header['RefundValue'] = 'RefundValue';
		$header['ReasonCode'] = 'ReasonCode';
		$rows[] = $header;
		
		
		foreach($records as $record){
			$row = array();
			$record->load($record->getId());
			$this->_helper()->log("Exporting refund ".$record->getId(), __CLASS__.'::'.__METHOD__, Zend_Log::DEBUG);
			$row['RefundDate'] = $record->getData('refund_date');
			$row['MerchantReference'] = $record->getData('merchant_reference');
			$row['OrderID'] = $record->getData('order_id');
			$row['Currency'] = $record->getData('currency');
			$row['RefundValue'] = $record->getData('refund_value');
			$row['ReasonCode'] = $record->getData('reason_code');
			$rows[] = $row;
		}
		if($csv->saveData($tmp_file, $rows)){
			$records->walk('markAsExported');
			$this->_helper()->log("Finished CSV export to ".$tmp_file, __CLASS__.'::'.__METHOD__);
			return $tmp_file;
		}
		
		return FALSE;
	}
	
	/**RefundDate, MerchantReference, OrderID, Currency, RefundValue, ReasonCode
	 * 
	 * @return bool
	 */
	public function markAsExported()
	{
		$this->setData('export_date', date('Y-m-d H:i:s'));
		if($this->save()){
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
	 * 
	 * @return bool
	 */
	public function createRefundRecord($amount)
	{
		$data = Mage::app()->getRequest()->getParams();
		$order = $this->_getOrderFromRequest($data);
		$reason_code = $this->_getReasonCodeFromRequest($data);
		$merchant_reference = $this->_helper()->getPayLaterConfigReference('merchant');
		if(!$merchant_reference){
			Mage::throwException("Merchant reference is not set, unable to create PayLater refund.");
		}
		$currency_code = $this->_getISOCurrencyCode($order);
		
		$this->setData('merchant_reference', $merchant_reference);
		$this->setData('order_id', $order->getIncrementId());
		$this->setData('currency', $currency_code);
		$this->setData('refund_value', $amount);
		$this->setData('reason_code', $reason_code);
		
		if($this->save()){
			return TRUE;
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
		if(!isset($data['order_id']) || (isset($data['order_id']) && !$data['order_id'])){
			Mage::throwException("Order ID is not set, unable to create PayLater refund.");
		}
		$order = Mage::getModel('sales/order')->load($data['order_id']);
		if(!$order->getId()){
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
		if(!isset($data['creditmemo']) || (isset($data['creditmemo']) && empty($data['creditmemo']))){
			Mage::throwException("Request data does not contain item 'creditmemo', is this a refund request?");
		}
		if(!isset($data['creditmemo']['reason_for_refund']) || (isset($data['creditmemo']['reason_for_refund']) && !$data['creditmemo']['reason_for_refund'])){
			Mage::throwException("Request data does not contain a 'Reason For Refund', is this a refund request?");
		}
		$reason_code = $data['creditmemo']['reason_for_refund'];
		if(!in_array($reason_code, explode(PayLater_PayLater_Core_Interface::REASON_CODES_SEPARATOR,PayLater_PayLater_Core_Interface::REASON_CODES))){
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
		$currency_const = "CURRENCY_".$currency."_ISO";
		$currency_code = constant('PayLater_PayLater_Core_Interface::'.$currency_const);
		if(!$currency_code){
			Mage::throwException("Error getting ISO code for '{$currency}', cannot create PayLater refund");
		}
		return $currency_code;
	}
}