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
 * Source model for DHL shipping methods for documentation
 *
 * @category   Magento
 * @package    Magento_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Usa\Model\Shipping\Carrier\Dhl\International\Source\Method;

class Unitofmeasure
{
    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $unitArr = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Dhl\International')->getCode('unit_of_measure');

        $returnArr = array();
        foreach ($unitArr as $key => $val) {
            $returnArr[] = array('value' => $key, 'label' => $val);
        }
        return $returnArr;
    }
}
