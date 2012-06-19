<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model of export format versions
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Source_Export_Format_Version
{
    /**#@+
     * Export versions
     */
    const EXPORT_VERSION_1 = 1;
    const EXPORT_VERSION_2 = 2;
    /**#@-*/

    /**
     * Prepare and return array of available export version file formats
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array(array(
            'label' => Mage::helper('Mage_ImportExport_Helper_Data')->__('-- Please Select --'),
            'value' => ''
        ));

        $options = $this->toArray();
        if (is_array($options) && count($options) > 0) {
            foreach ($options as $value => $label) {
                $optionArray[] = array(
                    'label' => $label,
                    'value' => $value
                );
            }
        }

        return $optionArray;
    }

    /**
     * Get possible export format versions
     *
     * @return array
     */
    public function toArray()
    {
        $helper = Mage::helper('Mage_ImportExport_Helper_Data');

        return array(
            self::EXPORT_VERSION_1 => $helper->__('Magento 1.7 format'),
            self::EXPORT_VERSION_2 => $helper->__('Magento 2.0 format'),
        );
    }
}
