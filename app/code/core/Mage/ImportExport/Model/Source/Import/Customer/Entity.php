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
 * Source import customer entity model (the same as for export)
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Source_Import_Customer_Entity
{
    /**
     * Prepare and return array of import customer entities ids and their names
     *
     * @return array
     */
    public function toOptionArray()
    {
        return Mage_ImportExport_Model_Config::getModelsComboOptions(
            Mage_ImportExport_Model_Import::CONFIG_KEY_CUSTOMER_ENTITIES
        );
    }
}
