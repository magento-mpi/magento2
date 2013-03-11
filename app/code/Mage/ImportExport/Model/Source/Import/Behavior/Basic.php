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
 * Import behavior source model used in import for product and customer import entities.
 * Source model saved to maintain compatibility with Magento 1.* import.
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Source_Import_Behavior_Basic
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
            Mage_ImportExport_Model_Import::BEHAVIOR_APPEND
                => $this->_helper('Mage_ImportExport_Helper_Data')->__('Append Complex Data'),
            Mage_ImportExport_Model_Import::BEHAVIOR_REPLACE
                => $this->_helper('Mage_ImportExport_Helper_Data')->__('Replace Existing Complex Data'),
            Mage_ImportExport_Model_Import::BEHAVIOR_DELETE
                => $this->_helper('Mage_ImportExport_Helper_Data')->__('Delete Entities'),
        );
    }

    /**
     * Get current behaviour code
     *
     * @return string
     */
    public function getCode()
    {
        return 'basic';
    }
}
