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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order shippment API
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Shipment_Api extends Mage_Sales_Model_Api_Resource
{
    public function __construct()
    {
        $this->_attributesMap['shipment'] = array('shipment_id' => 'entity_id');

        $this->_attributesMap['shipment_item'] = array('item_id'    => 'entity_id');

        $this->_attributesMap['shipment_comment'] = array('comment_id' => 'entity_id');

        $this->_attributesMap['shipment_track'] = array('track_id'   => 'entity_id');
    }

    /**
     * Retrive shipments by filters
     *
     * @param array $filters
     * @return array
     */
    public function items($filters = null)
    {
        //TODO: add full name logic
        $collection = Mage::getResourceModel('sales/order_shipment_collection')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('total_qty')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
            ->joinAttribute('order_increment_id', 'order/increment_id', 'order_id', null, 'left')
            ->joinAttribute('order_created_at', 'order/created_at', 'order_id', null, 'left');

        if (is_array($filters)) {
            try {
                foreach ($filters as $field => $value) {
                    if (isset($this->_filtersMap[$field])) {
                        $field = $this->_filtersMap[$field];
                    }

                    $collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }

        $result = array();

        foreach ($collection as $shipment) {
            $result[] = $this->_getAttributes($shipment, 'shipment');
        }

        return $result;
    }

    /**
     * Retrieve shipment information
     *
     * @param string $shipmentIncrementId
     * @return array
     */
    public function info($shipmentIncrementId)
    {
        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

        /* @var $shipment Mage_Sales_Model_Order_Shipment */

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        $result = $this->_getAttributes($shipment, 'shipment');

        $result['items'] = array();
        foreach ($shipment->getAllItems() as $item) {
            $result['items'][] = $this->_getAttributes($item, 'shipment_item');
        }

        $result['tracks'] = array();
        foreach ($shipment->getAllTracks() as $track) {
            $result['tracks'][] = $this->_getAttributes($track, 'shipment_track');
        }

        $result['comments'] = array();
        foreach ($shipment->getCommentsCollection() as $comment) {
            $result['comments'][] = $this->_getAttributes($comment, 'shipment_comment');
        }

        return $result;
    }

    /**
     * Create new shipment for order
     *
     * @param string $orderIncrementId
     * @param array $itemsQty
     * @param string $comment
     * @param booleam $email
     * @param boolean $includeComment
     * @return string
     */
    public function create($orderIncrementId, $itemsQty = array(), $comment = null, $email = false, $includeComment = false)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        /**
          * Check order existing
          */
        if (!$order->getId()) {
             $this->_fault('order_not_exists');
        }

        /**
         * Check shipment create availability
         */
        if (!$order->canShip()) {
             $this->_fault('data_invalid', Mage::helper('sales')->__('Can not do shipment for order.'));
        }

        $convertor   = Mage::getModel('sales/convert_order');
        $shipment    = $convertor->toShipment($order);
         /* @var $shipment Mage_Sales_Model_Order_Shipment */

        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getQtyToShip()) {
                continue;
            }
            if ($orderItem->getIsVirtual()) {
                continue;
            }
            $item = $convertor->itemToShipmentItem($orderItem);
            if (isset($itemsQty[$orderItem->getId()])) {
                $qty = $itemsQty[$orderItem->getId()];
            }
            else {
                $qty = $orderItem->getQtyToShip();
            }
            $item->setQty($qty);
        	$shipment->addItem($item);
        }
        $shipment->register();
        $shipment->addComment($comment, $email && $includeComment);

        if ($email) {
            $shipment->setEmailSent(true);
        }

        $shipment->getOrder()->setIsInProcess(true);

        try {
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($shipment)
                ->addObject($shipment->getOrder())
                ->save();

            $shipment->sendEmail($email, ($includeComment ? $comment : ''));
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $shipment->getIncrementId();
    }

    /**
     * Add tracking number to order
     *
     * @param string $shipmentIncrementId
     * @param string $carrier
     * @param string $title
     * @param string $trackNumber
     * @return int
     */
    public function addTrack($shipmentIncrementId, $carrier, $title, $trackNumber)
    {
        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

        /* @var $shipment Mage_Sales_Model_Order_Shipment */

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        $carriers = $this->_getCarriers($shipment);

        if (!isset($carriers[$carrier])) {
            $this->_fault('data_invalid', Mage::helper('sales')->__('Invalid carrier specified.'));
        }

        $track = Mage::getModel('sales/order_shipment_track')
                	->setNumber($trackNumber)
                    ->setCarrierCode($carrier)
                    ->setTitle($title);

        $shipment->addTrack($track);

        try {
            $shipment->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $track->getId();
    }

    /**
     * Remove tracking number
     *
     * @param string $shipmentIncrementId
     * @param int $trackId
     * @return boolean
     */
    public function removeTrack($shipmentIncrementId, $trackId)
    {
        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

        /* @var $shipment Mage_Sales_Model_Order_Shipment */

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        if(!$track = $shipment->getTrackById($trackId)) {
            $this->_fault('track_not_exists');
        }

        try {
            $track->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('track_not_deleted', $e->getMessage());
        }

        return true;
    }

    /**
     * Retrieve tracking number info
     *
     * @param string $shipmentIncrementId
     * @param int $trackId
     * @return mixed
     */
    public function infoTrack($shipmentIncrementId, $trackId)
    {
         $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

        /* @var $shipment Mage_Sales_Model_Order_Shipment */

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        if(!$track = $shipment->getTrackById($trackId)) {
            $this->_fault('track_not_exists');
        }

        /* @var $track Mage_Sales_Model_Order_Shipment_Track */
        $info = $track->getNumberDetail();

        if (is_object($info)) {
            $info = $info->toArray();
        }

        return $info;
    }

    /**
     * Add comment to shipment
     *
     * @param string $shipmentIncrementId
     * @param string $comment
     * @param boolean $email
     * @param boolean $includeInEmail
     * @return boolean
     */
    public function addComment($shipmentIncrementId, $comment, $email = false, $includeInEmail = false)
    {
        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

        /* @var $shipment Mage_Sales_Model_Order_Shipment */

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }


        try {
            $shipment->addComment($comment, $email);
            $shipment->sendUpdateEmail($email, ($includeInEmail ? $comment : ''));
            $shipment->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Retrieve allowed shipping carriers for specified order
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function getCarriers($orderIncrementId)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        /**
          * Check order existing
          */
        if (!$order->getId()) {
            $this->_fault('order_not_exists');
        }

        return $this->_getCarriers($order);
    }

    /**
     * Retrieve shipping carriers for specified order
     *
     * @param Mage_Eav_Model_Entity_Abstract $object
     * @return array
     */
    protected function _getCarriers($object)
    {
        $carriers = array();
        $carrierInstances = Mage::getSingleton('shipping/config')->getAllCarriers(
            $object->getStoreId()
        );

        $carriers['custom'] = Mage::helper('sales')->__('Custom Value');
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }

        return $carriers;
    }

} // Class Mage_Sales_Model_Order_Shipment_Api End