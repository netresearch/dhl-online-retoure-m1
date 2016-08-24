<?php
/**
 * Dhl OnlineRetoure
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Dhl
 * @package     Dhl_OnlineRetoure
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Dhl_OnlineRetoure_Test_Block_Customer_Address_EditTest
 *
 * @category    Dhl
 * @package     Dhl_OnlineRetoure
 * @author      André Herrn <andre.herrn@netresearch.de>
 * @author      Christoph Aßmann <christoph.assmann@netresearch.de>
 */
class Dhl_OnlineRetoure_Test_Block_Customer_Address_EditTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Set up controller params
     */
    protected function setUp()
    {
        $sessionMock = $this->getModelMock('customer/session', array('init', 'renewSession', 'start'));
        $this->replaceByMock('model', 'customer/session', $sessionMock);

        $baseUrl = Mage::getStoreConfig('web/unsecure/base_url');
        $this->app()->getRequest()->setBaseUrl($baseUrl);

        parent::setUp();
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    protected function getCurrentOrder()
    {
        if (!Mage::registry('current_order')) {
            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load(13);
            Mage::register('current_order', $order);
        }

        return Mage::registry('current_order');
    }

    /**
     * @return Dhl_OnlineRetoure_Block_Customer_Address_Edit
     */
    protected function getEditBlock()
    {
        return Mage::app()->getLayout()->createBlock('dhlonlineretoure/customer_address_edit');
    }

    /**
     * @loadFixture config.yaml
     * @loadFixture customers.yaml
     * @loadFixture orders.yaml
     */
    public function testGetNameBlockHtml()
    {
        $order     = $this->getCurrentOrder();
        $editBlock = $this->getEditBlock();

        /** @var $editBlock Dhl_OnlineRetoure_Block_Customer_Address_Edit */
        $nameBlockHtml = $editBlock->getNameBlockHtml();
        $this->assertThat($nameBlockHtml, $this->stringContains($order->getShippingAddress()->getLastname()));
    }

    /**
     * @loadFixture config.yaml
     * @loadFixture customers.yaml
     * @loadFixture orders.yaml
     */
    public function testGetBackUrl()
    {
        $order     = $this->getCurrentOrder();
        $editBlock = $this->getEditBlock();

        $session = $this->getModelMock('core/url', array('getUseSession'));
        $session->expects($this->any())
                ->method('getUseSession')
                ->will($this->returnValue(false));
        $this->replaceByMock('model', 'core/url', $session);

        $this->assertStringEndsWith(
            sprintf("sales/order/view/order_id/%d/", $order->getId()),
            $editBlock->getBackUrl()
        );
    }

    /**
     * @loadFixture config.yaml
     * @loadFixture customers.yaml
     * @loadFixture orders.yaml
     */
    public function testGetSaveUrl()
    {
        $order     = $this->getCurrentOrder();
        $hash      = 'foo';

        $session = $this->getModelMock('core/url', array('getUseSession'));
        $session->expects($this->any())
                ->method('getUseSession')
                ->will($this->returnValue(false));
        $this->replaceByMock('model', 'core/url', $session);

        $blockMock = $this->getBlockMock('dhlonlineretoure/customer_address_edit', array('getRequestHash'));
        $blockMock->expects($this->any())
                ->method('getRequestHash')
                ->will($this->onConsecutiveCalls('', $hash));
        $this->replaceByMock('block', 'dhlonlineretoure/customer_address_edit', $blockMock);

        $editBlock = $this->getEditBlock();

        // INTERNAL REQUEST
        $saveUrl = $editBlock->getSaveUrl();
        $this->assertStringEndsWith(sprintf("?order_id=%d", $order->getId()), $saveUrl);
        $this->assertNotContains('hash', $saveUrl);

        // EXTERNAL REQUEST
        $saveUrl = $editBlock->getSaveUrl();
        $this->assertContains(sprintf("order_id=%d", $order->getId()), $saveUrl);
        $this->assertContains(sprintf("hash=%s", $hash), $saveUrl);
    }
}
