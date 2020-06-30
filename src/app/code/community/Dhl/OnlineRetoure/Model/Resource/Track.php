<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Dhl_OnlineRetoure_Model_Resource_Track_Collection
 *
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Resource_Track extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * primary key is foreign key to order table.
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * Resource initialization.
     */
    public function _construct()
    {
        $this->_init('dhlonlineretoure/track', 'order_id');
    }
}
