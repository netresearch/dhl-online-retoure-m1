<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Dhl_OnlineRetoure_Model_Resource_Track_Collection
 *
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Resource_Track_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('dhlonlinretoure/track', 'dhlonlineretoure/track');
    }
}
