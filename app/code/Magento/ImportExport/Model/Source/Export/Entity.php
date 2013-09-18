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
 * Source export entity model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Model_Source_Export_Entity implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Prepare and return array of export entities ids and their names
     *
     * @return array
     */
    public function toOptionArray()
    {
        return Magento_ImportExport_Model_Config::getModelsComboOptions(
            Magento_ImportExport_Model_Export::CONFIG_KEY_ENTITIES, true
        );
    }
}
