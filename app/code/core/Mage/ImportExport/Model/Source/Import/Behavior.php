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
 * Source import behavior model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Source_Import_Behavior
{
    /**
     * Prepare and return array of import behavior.
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
                'value' => Mage_ImportExport_Model_Import::BEHAVIOR_APPEND,
                'label' => $helper->__('Append Complex Data')
            ),
            array(
                'value' => Mage_ImportExport_Model_Import::BEHAVIOR_REPLACE,
                'label' => $helper->__('Replace Existing Complex Data')
            ),
            array(
                'value' => Mage_ImportExport_Model_Import::BEHAVIOR_DELETE,
                'label' => $helper->__('Delete Entities')
            )
        );
    }
}
