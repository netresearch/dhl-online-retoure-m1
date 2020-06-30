<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Class Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocumentPosition
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocumentPosition implements JsonSerializable
{
    /**
     * @var string
     */
    public $positionDescription;

    /**
     * @var int
     */
    public $count;

    /**
     * @var int
     */
    public $weightInGrams;

    /**
     * @var float
     */
    public $values;

    /**
     * @var string
     */
    public $originCountry;

    /**
     * @var string
     */
    public $articleReference;

    /**
     * @var string
     */
    public $tarifNumber;

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
