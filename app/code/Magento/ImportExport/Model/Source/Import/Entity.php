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

class Entity implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\ImportExport\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\ImportExport\Model\Config $config
     */
    public function __construct(\Magento\ImportExport\Model\Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Prepare and return array of import entities ids and their names
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_config->getModelsComboOptions(
            \Magento\ImportExport\Model\Import::CONFIG_KEY_ENTITIES, true
        );
    }
}
