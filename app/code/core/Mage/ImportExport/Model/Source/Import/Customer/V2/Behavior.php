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
 * Source import behavior model for version 2 import
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Source_Import_Customer_V2_Behavior
{
    /**
     * Prepare and return option array of import behavior for version 2 import
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
     * Get array of import behaviors for version 2 import
     *
     * @return array
     */
    public function toArray()
    {
        $helper = Mage::helper('Mage_ImportExport_Helper_Data');

        return array(
            Mage_ImportExport_Model_Import::BEHAVIOR_V2_ADD_UPDATE => $helper->__('Add/Update Complex Data'),
            Mage_ImportExport_Model_Import::BEHAVIOR_V2_DELETE     => $helper->__('Delete Entities'),
            Mage_ImportExport_Model_Import::BEHAVIOR_V2_CUSTOM     => $helper->__('Custom Action'),
        );
    }
}
