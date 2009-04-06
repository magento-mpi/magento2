<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Amazon Order Document Api
 *
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Api_Cba_Document extends Varien_Object
{
    const MESSAGE_TYPE_ADJUSTMENT       = '_POST_PAYMENT_ADJUSTMENT_DATA_';
    const MESSAGE_TYPE_FULFILLMENT      = '_POST_ORDER_FULFILLMENT_DATA_';
    const MESSAGE_TYPE_ACKNOWLEDGEMENT  = '_POST_ORDER_ACKNOWLEDGEMENT_DATA_';

    protected $_wsdlUri = null;
    protected $_merchantInfo = array();
    protected $_client = null;
    protected $_result = null;
    protected $_options = array(
        'trace'     => true,
        'timeout'   => '20',
    );

    protected function _construct()
    {
        parent::_construct();
    }

    public function setWsdlUri($wsdlUri)
    {
        $this->_wsdlUri = $wsdlUri;
        return $this;
    }

    public function getWsdlUri()
    {
        return $this->_wsdlUri;
    }

    public function setMerchantInfo(array $merchantInfo = array())
    {
        $this->_merchantInfo = $merchantInfo;
        return $this;
    }

    public function getMerchantInfo()
    {
        return $this->_merchantInfo;
    }

    public function getMerchantIdentifier()
    {
        if (array_key_exists('merchantIdentifier', $this->_merchantInfo)) {
            return $this->_merchantInfo['merchantIdentifier'];
        }
        return null;
    }

    /**
     * Return Soap object
     *
     * @return SOAP_Client
     */
    public function getClient()
    {
        return $this->_client;
    }

    public function init($login, $password)
    {
        if ($this->getWsdlUri()) {
            $this->_client = null;
            $auth = array('user' => $login, 'pass' => $password);
            try {
                set_include_path(
                     BP . DS . 'lib' . DS . 'PEAR' . PS . get_include_path()
                );
                require_once 'SOAP/Client.php';
                $this->_client = new SOAP_Client($this->getWsdlUri(), true, false, $auth, false);
            } catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
        }
        return $this;
    }

    protected function _createAttachment($document)
    {
        require_once 'SOAP/Value.php';
        $attachment = new SOAP_Attachment('doc', 'application/binary', null, $document);
        $attachment->options['attachment']['encoding'] = '8bit';
        $this->_options['attachments'] = 'Mime';
        return $attachment;
    }

    protected function _proccessRequest($method, $params)
    {
        if ($this->getClient()) {
            $this->_result = null;
            try {
                $this->_result = $this->getClient()
                    ->call($method, $params, $this->_options);
            } catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
        }
        return $this;
    }

    /**
     * Get order info
     *
     * @param string $aOrderId Amazon order id
     */
    public function getDocument($aOrderId)
    {
        $params = array(
            'merchant' => $this->getMerchantInfo(),
            'documentIdentifier' => $aOrderId
        );
        $this->_proccessRequest('getDocument', $params);

        require_once 'Mail/mimeDecode.php';
        $decoder = new Mail_mimeDecode($this->getClient()->xml);
        $decoder->decode(array(
            'include_bodies' => true,
            'decode_bodies'  => true,
            'decode_headers' => true,
        ));
        $xml = $decoder->_body;

        // remove the ending mime boundary
        $boundaryIndex = strripos($xml, '--xxx-WASP-CPP-MIME-Boundary-xxx');
        if (!($boundaryIndex === false)) {
            $xml = substr($xml, 0, $boundaryIndex);
        }

        return simplexml_load_string($xml, 'Varien_Simplexml_Element');
    }

    public function getPendingDocuments()
    {
        $params = array(
            'merchant' => $this->getMerchantInfo(),
            'messageType' => '_GET_ORDERS_DATA_'
        );
        $this->_proccessRequest('getAllPendingDocumentInfo', $params);
        if (!is_array($this->_result)) {
            $this->_result = array($this->_result);
        }
        return $this->_result;
    }

    /**
     * Enter description here...
     *
     * @param string $documentType
     * @param Mage_Sales_Model_Order $order
     */
    public function cancel($order)
    {
//        $this->getPendingDocuments();
        Zend_Debug::dump($this->getDocument('990002713')->asXML());
//        try {
//            Zend_Debug::dump($this->getClient()->getWire());
//        } catch (Exception $e) {
//            echo 'Exception';
//            Zend_Debug::dump($e->getMessage());
//        }
        Zend_Debug::dump($this->_result);
        die(__METHOD__.'::'.__LINE__);
        $_document = '<?xml version="1.0" encoding="UTF-8"?>
        <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
        <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>' . $this->getMerchantIdentifier() . '</MerchantIdentifier>
        </Header>
        <MessageType>OrderAcknowledgement</MessageType>';
        foreach ($order->getAllVisibleItems() as $item) {
            $_document .= '<Message>
                <MessageID>1</MessageID>
                <OperationType>Update</OperationType>
                <OrderAcknowledgement>
                    <AmazonOrderID>' . $order->getExtOrderId() . '</AmazonOrderID>
                    <StatusCode>Failure</StatusCode>
                    <Item>
                        <AmazonOrderItemCode>'. $item->getExtOrderItemId() . '</AmazonOrderItemCode>
                        <CancelReason>BuyerCanceled</CancelReason>
                    </Item>
                </OrderAcknowledgement>
            </Message>';
        }
        $_document .= '</AmazonEnvelope>';
        $params = array(
            'merchant' => $this->getMerchantInfo(),
            'messageType' => self::MESSAGE_TYPE_ACKNOWLEDGEMENT,
            'doc' => $this->_createAttachment($_document)
        );
        $this->_proccessRequest('postDocument', $params);
        Zend_Debug::dump($this->_result);
        Zend_Debug::dump($this->getClient()->getWire());
        die(__METHOD__.'::'.__LINE__);
        return $this->_result;
    }

    /**
     *
     * @param Mage_Sales_Model_Order $order
     */
    public function refund($order, $amount)
    {
        $_document = '<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
<Header>
    <DocumentVersion>1.01</DocumentVersion>
    <MerchantIdentifier>' . $this->getMerchantIdentifier() . '</MerchantIdentifier>
</Header>
<MessageType>OrderAdjustment</MessageType>';

        foreach ($order->getAllVisibleItems() as $item) {
            /* @var $item Mage_Sales_Model_Order_Item */
            $itemAmount = $item->getQtyRefunded()*$item->getBasePrice();
            $itemTaxAmount = ($item->getBaseTaxAmount()/$item->getQtyInvoiced())*$item->getQtyRefunded();
            /** @todo
                    calculate tax for one item
                    calculate shipping for one item
                    calculate shipping tax for one item
            */
            $itemShippingAmount = $order->getShippingAmount();
            $itemShippingTaxAmount = 0;
            $_document .= '<Message>
                            <MessageID>' . $item->getId() . '</MessageID>
                            <OrderAdjustment>
                                <AmazonOrderID>' . $order->getExtOrderId() . '</AmazonOrderID>
                                <AdjustedItem>
                                    <AmazonOrderItemCode>'. $item->getExtOrderItemId() . '</AmazonOrderItemCode>
                                    <AdjustmentReason>GeneralAdjustment</AdjustmentReason>
                                    <ItemPriceAdjustments>
                                        <Component>
                                            <Type>Principal</Type>
                                            <Amount currency="USD">' . $itemAmount . '</Amount>
                                        </Component>'
                                        /*.'<Component>
                                            <Type>Shipping</Type>
                                            <Amount currency="USD">' . $itemShippingAmount . '</Amount>
                                        </Component>'*/
                                        .'<Component>
                                            <Type>Tax</Type>
                                            <Amount currency="USD">' . $itemTaxAmount . ' </Amount>
                                        </Component>'
                                        /*.'<Component>
                                            <Type>ShippingTax</Type>
                                            <Amount currency="USD">' . $itemShippingTaxAmount . '</Amount>
                                        </Component>'*/
                                    .'</ItemPriceAdjustments>';
            /** @todo
                    calculate promotion
            */
            $promotion = false;
            if ($promotion) {
                $_document .= '<PromotionAdjustments>
                                        <PromotionClaimCode>ABC123</PromotionClaimCode>
                                        <MerchantPromotionID>12345678</MerchantPromotionID>
                                        <Component>
                                            <Type>Principal</Type>
                                            <Amount currency="USD">-1.00</Amount>
                                        </Component>
                                    </PromotionAdjustments>';
            }
            $_document .= '</AdjustedItem>
                        </OrderAdjustment>
                    </Message>';
        }
        /** @todo
                promotion adjustment
        */
        $_document .= '</AmazonEnvelope>';
        $params = array(
            'merchant' => $this->getMerchantInfo(),
            'messageType' => self::MESSAGE_TYPE_ADJUSTMENT,
            'doc' => $this->_createAttachment($_document)
        );
        $this->_proccessRequest('postDocument', $params);
        return $this->_result;
    }

    public function sendTrackNumber($aOrderId, $carrierCode, $carrierMethod, $trackNumber)
    {
        $fulfillmentDate = gmdate('Y-m-d\TH:i:s');
        $_document = '<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
<Header>
    <DocumentVersion>1.01</DocumentVersion>
    <MerchantIdentifier>' . $this->getMerchantIdentifier() . '</MerchantIdentifier>
</Header>
<MessageType>OrderFulfillment</MessageType>
    <Message>
        <MessageID>1</MessageID>
        <OrderFulfillment>
            <AmazonOrderID>' . $aOrderId . '</AmazonOrderID>
            <FulfillmentDate>' . $fulfillmentDate . '</FulfillmentDate>
            <FulfillmentData>
                <CarrierCode>' . $carrierCode . '</CarrierCode>
                <ShippingMethod>' . $carrierMethod . '</ShippingMethod>
                <ShipperTrackingNumber>' . $trackNumber .'</ShipperTrackingNumber>
            </FulfillmentData>
        </OrderFulfillment>
    </Message>
</AmazonEnvelope>';
        $params = array(
            'merchant' => $this->getMerchantInfo(),
            'messageType' => self::MESSAGE_TYPE_FULFILLMENT,
            'doc' => $this->_createAttachment($_document)
        );
        $this->_proccessRequest('postDocument', $params);
        return $this->_result;
    }
}
