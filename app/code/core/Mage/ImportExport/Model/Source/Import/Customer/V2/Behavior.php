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
     * Prepare and return array of import behavior for version 2 import
     *
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('Mage_ImportExport_Helper_Data');

        return array(
            array(
                'value' => '',
                'label' => $helper->__('-- Please Select --')
            ),
            array(
                'value' => Mage_ImportExport_Model_Import::BEHAVIOR_V2_ADD_UPDATE,
                'label' => $helper->__('Add/Update Complex Data')
            ),
            array(
                'value' => Mage_ImportExport_Model_Import::BEHAVIOR_V2_DELETE,
                'label' => $helper->__('Delete Entities')
            ),
            array(
                'value' => Mage_ImportExport_Model_Import::BEHAVIOR_V2_CUSTOM,
                'label' => $helper->__('Custom Action')
            ),
        );
    }
}
