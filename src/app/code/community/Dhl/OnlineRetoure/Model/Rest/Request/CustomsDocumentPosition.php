<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Class Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocumentPosition
 *
 * @category Dhl
 * @package  Dhl_OnlineRetoure
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link     https://www.netresearch.de/
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
     * @return string
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}