<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Class Dhl_OnlineRetoure_Model_Adminhtml_Observer
 *
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Observer
{
    /**
     * @var Mage_Core_Model_Layout
     */
    private $layout;

    /**
     * @var Dhl_OnlineRetoure_Helper_Validate
     */
    private $helper;

    /**
     * Dhl_OnlineRetoure_Model_Observer constructor.
     */
    public function __construct()
    {
        $this->layout = Mage::getSingleton('core/layout');
        $this->helper = Mage::helper('dhlonlineretoure/validate');
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function addReturnShipmentLabelButton(Varien_Event_Observer $observer)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/ship')) {
            return;
        }

        /** @var Mage_Adminhtml_Block_Sales_Order_View $block */
        $block = $this->layout->getBlock('sales_order_edit');
        /** @var Mage_Sales_Model_Order $order */
        $order = $block->getOrder();

        try {
            // check if the order qualifies for return label creation.
            $this->helper->isOrderValid($order);
        } catch (\Dhl_OnlineRetoure_Exception_OrderValidationException $exception) {
            // no, it does not.
            return;
        }

        $hash = $this->helper->createHashForOrder($order);
        $params = $this->helper->getUrlParams($order->getId(), $hash);
        $url = $order->getStore()->getUrl('dhlonlineretoure/create/index', $params);

        $block->addButton(
            'create_retoure_label',
            array(
                'label' => $this->helper->__('Create Return Label'),
                'onclick' => 'window.open(\'' . $url . '\')',
                'class' => 'scalable ',
            ),
            1,
            20
        );
    }
}
