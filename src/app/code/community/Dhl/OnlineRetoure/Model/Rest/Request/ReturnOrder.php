<?php
/**
 * See LICENSE.md for license details.
 */

use Dhl_OnlineRetoure_Model_Rest_Request_SimpleAddress as SenderAddress;
use Dhl_OnlineRetoure_Model_Rest_Request_CustomsDocument as CustomsDocument;

/**
 * Class Dhl_OnlineRetoure_Model_Rest_Request_ReturnOrder
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Rest_Request_ReturnOrder implements JsonSerializable
{
    /**
     * @var string
     */
    public $receiverId;

    /**
     * @var string
     */
    public $customerReference;

    /**
     * @var string
     */
    public $shipmentReference;

    /**
     * @var SenderAddress
     */
    public $senderAddress;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $telephoneNumber;

    /**
     * @var int
     */
    public $weightInGrams;

    /**
     * @var float
     */
    public $value;

    /**
     * @var CustomsDocument
     */
    public $customsDocument;

    /**
     * @var string
     */
    public $returnDocumentType;

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
