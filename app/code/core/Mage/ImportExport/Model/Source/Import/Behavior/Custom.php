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
class Mage_ImportExport_Model_Source_Import_Behavior_Custom
    extends Mage_ImportExport_Model_Source_Import_BehaviorAbstract
{
    /**
     * Get possible behaviours
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            Mage_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE
                => $this->_helper('Mage_ImportExport_Helper_Data')->__('Add/Update Complex Data'),
            Mage_ImportExport_Model_Import::BEHAVIOR_DELETE
                => $this->_helper('Mage_ImportExport_Helper_Data')->__('Delete Entities'),
            Mage_ImportExport_Model_Import::BEHAVIOR_CUSTOM
                => $this->_helper('Mage_ImportExport_Helper_Data')->__('Custom Action'),
        );
    }

    /**
     * Get current behaviour code
     *
     * @return string
     */
    public function getCode()
    {
        return 'custom';
    }
}
