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
     * Close sales order
     *
     * @param Fixture $fixture [optional]
     */
    public function execute(Fixture $fixture = null)
    {
        //Click the Ship button
        $orderId = $fixture->getOrderId();
        $orderId = substr($orderId, 1);
        $orderId = (int)$orderId;
        //Click Ship button and create a new shipment page
        $url = $_ENV['app_backend_url'] . 'admin/order_shipment/start/order_id/' . $orderId;
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), array());
        $response = $curl->read();
        $curl->close();

        preg_match_all('/shipment\[items\]\[[a-z0-9]+\]/', $response, $shipmentItems);

        // Fill out the Items to ship quantities and click Submit Shipment
        $data = array();
        foreach($shipmentItems as $element) {
            foreach($element as $key) {
                $data[$key] = '1';
            }
        }

        $url = $_ENV['app_backend_url'] . 'admin/order_shipment/save/order_id/' . $orderId;
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Submitting shipment by curl handler was not successful! Response: $response");
        }

        // Click Invoice button if the payment method was not PayPal Standard
        if(!($fixture instanceof \Magento\Sales\Test\Fixture\PaypalStandardOrder)) {
            //Click Invoice button and create a new invoice page
            $url = $_ENV['app_backend_url'] . 'sales/order_invoice/start/order_id/' . $orderId;
            $curl = new BackendDecorator(new CurlTransport(), new Config);
            $curl->addOption(CURLOPT_HEADER, 1);
            $curl->write(CurlInterface::POST, $url, '1.0', array(), array());
            $response = $curl->read();
            $curl->close();

            preg_match_all('/invoice\[items\]\[[a-z0-9]+\]/', $response, $invoiceItems);

            // Fill out the Items to invoice quantities and click Submit Invoice
            $data = array();
            foreach($invoiceItems as $element) {
                foreach($element as $key) {
                    $data[$key] = '1';
                }
            }

            //Selectd Capture Online
            $data['invoice[capture_case]'] = 'online';

            $url = $_ENV['app_backend_url'] . 'sales/order_invoice/save/order_id/' . $orderId;
            $curl = new BackendDecorator(new CurlTransport(), new Config);
            $curl->addOption(CURLOPT_HEADER, 1);
            $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
            $response = $curl->read();
            $curl->close();

            if (!strpos($response, 'data-ui-id="messages-message-success"')) {
                throw new \Exception("Submitting Invoice by curl handler was not successful! Response: $response");
            }
        }
    }
}
