<?php
/**
 * See LICENSE.md for license details.
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$adminVersion = Mage::getConfig()->getModuleConfig('Mage_Admin')->version;
if ($adminVersion == '1.6.0.0.1.2' || version_compare($adminVersion, '1.6.1.1', '>')) {
    $table = $installer->getTable('admin/permission_block');
    $installer->getConnection()->insertOnDuplicate(
        $table,
        array(
            'block_name' => 'dhlonlineretoure/sales_order_email_retoure',
            'is_allowed' => 1
        )
    );
}
