<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Class Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocument
 *
 * @package Dhl_OnlineRetoure
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocument implements JsonSerializable
{
    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $originalShipmentNumber;

    /**
     * @var string
     */
    public $originalOperator;

    /**
     * @var string
     */
    public $acommpanyingDocument;

    /**
     * @var string
     */
    public $originalInvoiceNumber;

    /**
     * @var string
     */
    public $originalInvoiceDate;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocumentPosition[]
     */
    public $positions;

    /**
     * @return string[]
     */
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
