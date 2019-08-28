<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Class Dhl_OnlineRetoure_Model_Rest_Request_Country
 *
 * @category Dhl
 * @package  Dhl_OnlineRetoure
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link     https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Rest_Request_Country implements JsonSerializable
{
    /**
     * @var string
     */
    public $countryISOCode;

    /**
     * @var String
     */
    public $country;

    /**
     * @var string
     */
    public $state;

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
       return get_object_vars($this);
    }
}
