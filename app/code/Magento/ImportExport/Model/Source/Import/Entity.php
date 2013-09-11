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
 * Source import entity model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ImportExport\Model\Source\Import;

class Entity
{
    /**
     * Prepare and return array of import entities ids and their names
     *
     * @return array
     */
    public function toOptionArray()
    {
        return \Magento\ImportExport\Model\Config::getModelsComboOptions(
            \Magento\ImportExport\Model\Import::CONFIG_KEY_ENTITIES, true
        );
    }
}
