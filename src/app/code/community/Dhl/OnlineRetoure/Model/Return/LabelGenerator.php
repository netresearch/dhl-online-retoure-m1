<?php
/**
 * See LICENSE.md for license details.
 */

/**
 * Label Generator.
 *
 * @package Dhl_OnlineRetoure
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Dhl_OnlineRetoure_Model_Return_LabelGenerator
{
    /**
     * Generate PDF file name.
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $shipmentNumber
     * @return string
     */
    public function getFilename(\Mage_Sales_Model_Order $order, $shipmentNumber)
    {
        $filename = sprintf(
            '%s-%s-(%s).pdf',
            $order->getStore()->getFrontendName(),
            $order->getRealOrderId(),
            $shipmentNumber
        );

        return str_replace(' ', '_', $filename);
    }

    /**
     * Isolated image to PDF conversion.
     *
     * @see \Mage_Adminhtml_Sales_Order_ShipmentController::_createPdfPageFromImageString
     *
     * @param string $imageString
     * @return bool|\Zend_Pdf_Page
     * @throws \Zend_Pdf_Exception
     */
    public function createPdfPageFromImageString($imageString)
    {
        $image = imagecreatefromstring($imageString);
        if (!$image) {
            return false;
        }

        $xSize = imagesx($image);
        $ySize = imagesy($image);
        $page = new \Zend_Pdf_Page($xSize, $ySize);

        imageinterlace($image, 0);
        $tmpFileName = sys_get_temp_dir() . DS . 'shipping_labels_' . uniqid(random_int(100, 999)) . time() . '.png';
        imagepng($image, $tmpFileName);
        $pdfImage = \Zend_Pdf_Image::imageWithPath($tmpFileName);
        $page->drawImage($pdfImage, 0, 0, $xSize, $ySize);
        unlink($tmpFileName);
        return $page;
    }

    /**
     * Create combined label PDF from web service response.
     *
     * @param string[] $response
     * @return string
     */
    public function combineLabelsPdf(array $response)
    {
        $labelData = base64_decode($response['labelData']);
        if (empty($response['qrLabelData'])) {
            return $labelData;
        }

        try {
            $page = $this->createPdfPageFromImageString(base64_decode($response['qrLabelData']));

            $pdfLabel = \Zend_Pdf::parse($labelData);
            $pdfLabel->pages[]= $page;
            $labelData = $pdfLabel->render();
        } catch (\Zend_Pdf_Exception $exception) {
            Mage::helper("dhlonlineretoure/data")->log($exception->getMessage(), \Zend_Log::ERR);
        }

        return $labelData;
    }
}
