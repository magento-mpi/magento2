<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for DHL shipping methods
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Usa_Model_Shipping_Carrier_Dhl_International_Source_Method_Abstract
{
    /**
     * Carrier Product Type Indicator
     *
     * @var string $_contentType
     */
    protected $_contentType;

    /**
     * Show 'none' in methods list or not;
     *
     * @var bool
     */
    protected $_noneMethod = false;

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        /* @var $carrierModel Mage_Usa_Model_Shipping_Carrier_Dhl_International */
        $carrierModel   = Mage::getSingleton('Mage_Usa_Model_Shipping_Carrier_Dhl_International');
        $dhlProducts    = $carrierModel->getDhlProducts($this->_contentType);

        $options = array();
        foreach ($dhlProducts as $code => $title) {
            $options[] = array('value' => $code, 'label' => $title);
        }

        if ($this->_noneMethod) {
            array_unshift($options, array('value' => '', 'label' => Mage::helper('Mage_Usa_Helper_Data')->__('None')));
        }

        return $options;
    }
}
