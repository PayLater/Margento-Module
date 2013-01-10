<?php

$installer = $this;

$configValuesMap = array(
	PayLater_PayLater_Core_Interface::XML_PATH_PAYLATER_EMAIL_TEMPLATE => PayLater_PayLater_Core_Interface::XML_PATH_PAYLATER_EMAIL_TEMPLATE_NODE,
	PayLater_PayLater_Core_Interface::XML_PATH_PAYLATER_EMAIL_GUEST_TEMPLATE => PayLater_PayLater_Core_Interface::XML_PATH_PAYLATER_EMAIL_GUEST_TEMPLATE_NODE
);

foreach ($configValuesMap as $configPath => $configValue) {
	$installer->setConfigData($configPath, $configValue);
}


$installer->startSetup();
$installer->run("
    INSERT INTO  `{$this->getTable('sales/order_status')}` (
        `status` ,
        `label`
    ) VALUES (
        'paylater_orphaned',  'PayLater Orphaned'
    );
	INSERT INTO  `{$this->getTable('sales/order_status')}` (
        `status` ,
        `label`
    ) VALUES (
        'paylater_failed',  'PayLater Failed'
    );
    INSERT INTO  `{$this->getTable('sales/order_status_state')}` (
        `status` ,
        `state` ,
        `is_default`
    ) VALUES (
        'status_code',  'processing',  '0'
    );
	INSERT INTO  `{$this->getTable('sales/order_status_state')}` (
        `status` ,
        `state` ,
        `is_default`
    ) VALUES (
        'paylater_orphaned',  'pending_payment',  '0'
    );
	INSERT INTO  `{$this->getTable('sales/order_status_state')}` (
        `status` ,
        `state` ,
        `is_default`
    ) VALUES (
        'paylater_failed',  'canceled',  '0'
    );
");
	
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_INFO_TEXT, array('type'=>'varchar'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_EMAIL_INFO_TEXT, array('type'=>'varchar'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_FEE_PRICE, array('type'=>'decimal'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_INSTALLMENTS_AMOUNT, array('type'=>'decimal'));
$installer->addAttribute('quote', PayLater_PayLater_Core_Interface::PAYLATER_TOTAL_TO_BE_PAID, array('type'=>'decimal'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_INFO_TEXT, array('type'=>'varchar'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_EMAIL_INFO_TEXT, array('type'=>'varchar'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_FEE_PRICE, array('type'=>'decimal'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_INSTALLMENTS_AMOUNT, array('type'=>'decimal'));
$installer->addAttribute('order', PayLater_PayLater_Core_Interface::PAYLATER_TOTAL_TO_BE_PAID, array('type'=>'decimal'));

$installer->endSetup();