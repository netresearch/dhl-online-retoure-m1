<?php
/**
* See LICENSE.md for license details.
*/

/**
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('sales/setup', 'sales_setup');

$idColumnDefinition = array(
    'identity'  => false,
    'unsigned'  => true,
    'nullable'  => false,
    'primary'   => true,
);

$shipmentNumber = array(
    'default' => '',
    'unsigned' => true,
    'nullable' => false,
);

$table = $installer->getConnection()
    ->newTable($installer->getTable('dhlonlineretoure/track'))
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, $idColumnDefinition, 'Entity Id')
    ->addColumn('shipment_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, $shipmentNumber, 'Shipment Number')
    ->addIndex(
        $installer->getIdxName('dhlonlineretoure/track', array('shipment_number')),
        array('shipment_number')
    )
    ->addForeignKey(
        $installer->getFkName('dhlonlineretoure/track', 'order_id', 'sales/order', 'entity_id'),
        'order_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('DHL Online Retoure Tracks');
$installer->getConnection()->createTable($table);
