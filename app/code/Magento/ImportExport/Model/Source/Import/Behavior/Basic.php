<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import behavior source model used in import for product and customer import entities.
 * Source model saved to maintain compatibility with Magento 1.* import.
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Model_Source_Import_Behavior_Basic
    extends Magento_ImportExport_Model_Source_Import_BehaviorAbstract
{
    /**
     * Get possible behaviours
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            Magento_ImportExport_Model_Import::BEHAVIOR_APPEND
                => __('Append Complex Data'),
            Magento_ImportExport_Model_Import::BEHAVIOR_REPLACE
                => __('Replace Existing Complex Data'),
            Magento_ImportExport_Model_Import::BEHAVIOR_DELETE
                => __('Delete Entities'),
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
