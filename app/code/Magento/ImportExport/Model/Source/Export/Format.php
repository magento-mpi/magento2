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
 * Source model of export file formats
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ImportExport\Model\Source\Export;

class Format
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
     * Prepare and return array of available export file formats.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $formats = \Magento\ImportExport\Model\Export::CONFIG_KEY_FORMATS;
        return $this->_config->getModelsComboOptions($formats);
    }
}
