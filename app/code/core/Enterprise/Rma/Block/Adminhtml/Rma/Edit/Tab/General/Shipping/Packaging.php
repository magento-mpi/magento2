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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shipment packaging
 *
 * @category    Enterprise
 * @package     Enterprise_RMA
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shipping_Packaging extends Mage_Adminhtml_Block_Template
{
    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getRma()
    {
        return Mage::registry('current_rma');
    }

    /**
     * Retrieve carrier
     */
    public function getCarrier()
    {
        return Mage::helper('enterprise_rma')->getCarrier(
            $this->getRequest()->getParam('method'),
            $this->getRma()->getStoreId()
        );
    }

    /**
     * Return container types of carrier
     *
     * @return array
     */
    public function getContainers()
    {
        $order = $this->getShipment()->getOrder();
        $storeId = $this->getShipment()->getStoreId();
        $address = $order->getShippingAddress();
        $carrier = $order->getShippingCarrier();
        $isIntl = $address->getCountryId() != Mage::getStoreConfig(
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
            $storeId
        );
        if ($carrier) {
            $params = array(
                'shipping_method' => $order->getShippingMethod(),
                'is_intl' => $isIntl
            );
            return $carrier->getContainerTypes($params);
        }
        return array();
    }

    /**
     * Return name of container type by its code
     *
     * @return string
     */
    public function getContainerTypeByCode($code)
    {
        $carrier = $this->getShipment()->getOrder()->getShippingCarrier();
        if ($carrier) {
            $containerTypes = $carrier->getContainerTypes();
            $containerType = !empty($containerTypes[$code]) ? $containerTypes[$code] : '';
            return $containerType;
        }
        return '';
    }


    /**
     * Can display customs value
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        return false;
        $storeId = $this->getShipment()->getStoreId();
        $order = $this->getShipment()->getOrder();
        $carrierCode = $order->getShippingCarrier()->getCarrierCode();
        $address = $order->getShippingAddress();
        $shipperAddressCountryCode = Mage::getStoreConfig('shipping/origin/country_id', $storeId);
        $recipientAddressCountryCode = $address->getCountryId();

        if (($carrierCode == 'fedex' || $carrierCode == 'dhl')
            && $shipperAddressCountryCode != $recipientAddressCountryCode) {
            return true;
        }
        return false;
    }
}
