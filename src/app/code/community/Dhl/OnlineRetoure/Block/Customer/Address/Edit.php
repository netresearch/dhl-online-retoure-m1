<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * DHL OnlineRetoure shipping address confirmation form
 *
 * @package Dhl_OnlineRetoure
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Block_Customer_Address_Edit extends Mage_Directory_Block_Data
{
    protected $_address;

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_address = $this->getOrder()->getShippingAddress();
        if (!$this->_address->getId()) {
            $this->_address->setPrefix($this->getCustomer()->getPrefix())
                ->setFirstname($this->getCustomer()->getFirstname())
                ->setMiddlename($this->getCustomer()->getMiddlename())
                ->setLastname($this->getCustomer()->getLastname())
                ->setSuffix($this->getCustomer()->getSuffix());
        }

        if ($postedData = Mage::getSingleton('customer/session')->getAddressFormData(true)) {
            $this->_address->addData($postedData);
        }

        return $this;
    }

    /**
     * Generate name block html
     *
     * @return string
     */
    public function getNameBlockHtml()
    {
        $nameBlock = $this->getLayout()->createBlock('customer/widget_name');
        $nameBlock->setData('object', $this->getAddress());

        return $nameBlock->toHtml();
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('sales/order/view', array('order_id' => $this->getOrder()->getId()));
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        /** @var Dhl_OnlineRetoure_Helper_Validate $helper */
        $helper = $this->helper('dhlonlineretoure/validate');
        $params = $helper->getUrlParams($this->getOrder()->getId(), $this->getRequestHash());
        return $this->getUrl('dhlonlineretoure/label/formPost', $params);
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        /** @var Dhl_OnlineRetoure_Helper_Data $helper */
        $helper = $this->helper('dhlonlineretoure/data');
        return $helper->getLoggedInCustomer();
    }

    /**
     * @return string
     */
    public function getCountryId()
    {
        if ($countryId = $this->getAddress()->getCountryId()) {
            return $countryId;
        }

        return parent::getCountryId();
    }

    /**
     * @return string
     */
    public function getRegionId()
    {
        return $this->getAddress()->getRegionId();
    }

    /**
     * @return Mage_Sales_Model_Order_Address
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * @return string
     */
    public function getRevocationPageUrl()
    {
        /** @var Dhl_OnlineRetoure_Model_Config $config */
        $config = Mage::getModel('dhlonlineretoure/config');
        $urlKey = $config->getCmsRevocationPage($this->getOrder()->getStoreId());
        if (!$urlKey) {
            return '';
        }

        return Mage::getUrl($urlKey);
    }

    /**
     * Obtain current hash that must be set on external requests
     * @return string
     */
    public function getRequestHash()
    {
        try {
            return $this->getRequest()->getQuery('hash', '');
        } catch (Exception $exception) {
            Mage::helper('dhlonlineretoure/data')->log($exception->getMessage());
            return '';
        }
    }

    /**
     * @return Mage_Sales_Model_Order|null
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }
}
