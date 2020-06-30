<?php
/**
 * See LICENSE.md for license details.
 */

use Dhl_OnlineRetoure_Model_Rest_Request_Country as Country;

/**
 * Class Dhl_OnlineRetoure_Model_Rest_Request_SimpleAddress
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Rest_Request_SimpleAddress implements JsonSerializable
{
    /**
     * @var string
     */
    public $name1;

    /**
     * @var string
     */
    public $name2;

    /**
     * @var string
     */
    public $name3;

    /**
     * @var string
     */
    public $streetName;

    /**
     * @var string
     */
    public $houseNumber;

    /**
     * @var string
     */
    public $postCode;

    /**
     * @var string
     */
    public $city;

    /**
     * @var Country
     */
    public $country;

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
