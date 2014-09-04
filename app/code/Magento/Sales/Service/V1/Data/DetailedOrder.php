<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

/**
 * Class DetailedOrder
 * @package Magento\Sales\Service\V1\Data
 */
class DetailedOrder extends Order
{
    const SHIPMENTS = 'shipments';
    const INVOICES = 'invoices';
    const CREDITMEMOS = 'creditmemos';

    /**
     * Get Shipments DataObject
     *
     * @return Shipment[]
     */
    public function getShipments()
    {
        return $this->_get(self::SHIPMENTS);
    }

    /**
     * Get Invoices DataObject
     *
     * @return Invoice[]
     */
    public function getInvoices()
    {
        return $this->_get(self::INVOICES);
    }

    /**
     * Get Creditmemo DataObject
     *
     * @return Creditmemo[]
     */
    public function getCreditmemos()
    {
        return $this->_get(self::CREDITMEMOS);
    }

} 