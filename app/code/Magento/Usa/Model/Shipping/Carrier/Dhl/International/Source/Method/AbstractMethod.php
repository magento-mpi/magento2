<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for DHL shipping methods
 *
 * @category   Magento
 * @package    Magento_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Usa\Model\Shipping\Carrier\Dhl\International\Source\Method;

abstract class AbstractMethod extends \Magento\Usa\Model\Shipping\Carrier\Dhl\International\Source\Method\Generic
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
        /* @var $carrierModel Magento_Usa_Model_Shipping_Carrier_Dhl_International */
        $carrierModel   = $this->_shippingDhlInt;
        $dhlProducts    = $carrierModel->getDhlProducts($this->_contentType);

        $options = array();
        foreach ($dhlProducts as $code => $title) {
            $options[] = array('value' => $code, 'label' => $title);
        }

        if ($this->_noneMethod) {
            array_unshift($options, array('value' => '', 'label' => __('None')));
        }

        return $options;
    }
}
