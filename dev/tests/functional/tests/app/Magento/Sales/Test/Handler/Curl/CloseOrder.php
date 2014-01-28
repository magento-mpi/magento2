<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class CloseOrder
 * Closes a sales order
 *
 * @package Magento\Sales\Test\Handler\Curl
 */
class CloseOrder extends Curl
{
    /**
     * @var string
     */
    protected $salesOrderUrl = 'sales/order/';

    /**
     * @var string
     */
    protected $startShipmentUrl = 'admin/order_shipment/start/order_id/';

    /**
     * @var string
     */
    protected $submitShiptmentUrl = 'admin/order_shipment/save/order_id/';

    /**
     * @var string
     */
    protected $startInvoiceUrl = 'sales/order_invoice/start/order_id/';

    /**
     * @var string
     */
    protected $submitInvoiceUrl = 'sales/order_invoice/save/order_id/';

    /**
     * @var string
     */
    protected $shipmentFieldsPattern = '/shipment\[items\]\[[a-z0-9]+\]/';

    /**
     * @var string
     */
    protected $invoiceFieldsPattern = '/invoice\[items\]\[[a-z0-9]+\]/';

    /**
     * Executes the CURL command with a given URL and data set
     *
     * @param string $url
     * @param array $data
     * @return string $response
     */
    protected function _executeCurl($url, $data = array())
    {
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();
        return $response;
    }

    /**
     * Populate the quantity fields with a value of 1
     *
     * @param array $elements
     * @return array
     */
    protected function _prepareData($elements)
    {
        $data = array();
        foreach($elements as $element) {
            foreach($element as $key) {
                $data[$key] = '1';
            }
        }
        return $data;
    }

    /**
     * Close sales order
     *
     * @param Fixture $fixture [optional]
     * @return void
     */
    public function execute(Fixture $fixture = null)
    {
        //Go to the Sales Order page and find the link for the order id to pass to the url
        $url = $_ENV['app_backend_url'] . $this->salesOrderUrl;
        $data = array();
        $response = $this->_executeCurl($url, $data);

        $searchUrl = '#href=\"' . $_ENV['app_backend_url'] . 'sales/order/view/order_id/[0-9]+/\">View#';

        preg_match($searchUrl, $response, $orderUrl);
        $urlSubStrings = explode('/',$orderUrl[0]);
        $orderId = $urlSubStrings[count($urlSubStrings)-2];

        //Click Ship button and create a new shipment page
        $url = $_ENV['app_backend_url'] . $this->startShipmentUrl . $orderId;
        $data = array();
        $response = $this->_executeCurl($url, $data);

        //Get the dynamic field names
        preg_match_all($this->shipmentFieldsPattern, $response, $shipmentItems);

        // Fill out the Items to ship quantities and click Submit Shipment
        $data = $this->_prepareData($shipmentItems);

        $url = $_ENV['app_backend_url'] . $this->submitShiptmentUrl . $orderId;
        $response = $this->_executeCurl($url, $data);

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("URL: $url\n"
            . "Submitting shipment by curl handler was not successful! Response: $response");
        }

        // Click Invoice button if the payment method was not PayPal Standard
        // Note:  a check can be added here for future tests that have a payment method
        // configured that has a payment action of 'Sale' instead of 'Authorization'
        // as clicking the Invoice button step can be skipped.
        if(!($fixture instanceof \Magento\Sales\Test\Fixture\PaypalStandardOrder)) {
            //Click Invoice button and create a new invoice page
            $url = $_ENV['app_backend_url'] . $this->startInvoiceUrl . $orderId;
            $data = array();
            $this->_executeCurl($url, $data);

            //Get the dynamic field names
            preg_match_all($this->invoiceFieldsPattern, $response, $invoiceItems);

            // Fill out the Items to invoice quantities and click Submit Invoice
            $data = $this->_prepareData($invoiceItems);

            //Select Capture Online
            $data['invoice[capture_case]'] = 'online';

            $url = $_ENV['app_backend_url'] . $this->submitInvoiceUrl . $orderId;
            $response = $this->_executeCurl($url, $data);

            if (!strpos($response, 'data-ui-id="messages-message-success"')) {
                throw new \Exception("Submitting Invoice by curl handler was not successful! Response: $response");
            }
        }
    }
}
