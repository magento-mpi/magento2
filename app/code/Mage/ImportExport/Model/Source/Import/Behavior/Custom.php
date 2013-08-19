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
 * Import behavior source model used for customers' components import entities.
 * Source model used in new import entities in Magento 2.0.
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
                => __('Add/Update Complex Data'),
            Mage_ImportExport_Model_Import::BEHAVIOR_DELETE
                => __('Delete Entities'),
            Mage_ImportExport_Model_Import::BEHAVIOR_CUSTOM
                => __('Custom Action'),
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
