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
     * Variable to store RMA instance
     *
     * @var null|Enterprise_Rma_Model_Rma
     */
    protected $_rma = null;

    /**
     * Declare rma instance
     *
     * @return  Enterprise_Rma_Model_Item
     */
    public function getRma()
    {
        if (is_null($this->_rma)) {
            $this->_rma = Mage::registry('current_rma');
        }
        return $this->_rma;
    }

    /**
     * Retrieve carrier
     *
     * @return string
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
        $order      = $this->getRma()->getOrder();
        $storeId    = $this->getRma()->getStoreId();
        $address    = $order->getShippingAddress();
        $carrier    = $this->getCarrier();

        $countryRecipient = Mage::getStoreConfig(Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID, $storeId);
        if ($carrier) {
            $params = new Varien_Object(array(
                'method' => $order->getShippingMethod(true)->getMethod(),
                'country_shipper' => $address->getCountryId(),
                'country_recipient' => $countryRecipient,
            ));
            return $carrier->getContainerTypes($params);
        }
        return array();
    }

    /**
     * Can display customs value
     *
     * @return bool
     */
    public function displayCustomsValue()
    {

        $storeId    = $this->getRma()->getStoreId();
        $order      = $this->getRma()->getOrder();
        $code       = $this->getRequest()->getParam('method');
        if (!empty($code)) {
            list($carrierCode, $methodCode) = explode('_', $code, 2);
            $address                        = $order->getShippingAddress();
            $shipperAddressCountryCode      = $address->getCountryId();
            $recipientAddressCountryCode    = Mage::helper('enterprise_rma')
                ->getReturnAddressModel($storeId)->getCountryId();

            if (($carrierCode == 'fedex' || $carrierCode == 'dhl')
                && $shipperAddressCountryCode != $recipientAddressCountryCode) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return delivery confirmation types of current carrier
     *
     * @return array
     */
    public function getDeliveryConfirmationTypes()
    {
        $storeId    = $this->getRma()->getStoreId();
        $code       = $this->getRequest()->getParam('method');
        if (!empty($code)) {
            list($carrierCode, $methodCode) = explode('_', $code, 2);
            $carrier    = Mage::helper('enterprise_rma')->getCarrier($carrierCode, $storeId);
            $countryId  = Mage::helper('enterprise_rma')->getReturnAddressModel($storeId)->getCountryId();
            $params = new Varien_Object(array('country_recipient' => $countryId));
            
            if ($carrier && is_array($carrier->getDeliveryConfirmationTypes($params))) {
                return $carrier->getDeliveryConfirmationTypes($params);
            }
        }
        return array();
    }
}
