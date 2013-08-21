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
 * Import adapter model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Model_Import_Adapter
{
    /**
     * Adapter factory. Checks for availability, loads and create instance of import adapter object.
     *
     * @param string $type Adapter type ('csv', 'xml' etc.)
     * @param mixed $options OPTIONAL Adapter constructor options
     * @throws Exception
     * @return Magento_ImportExport_Model_Import_SourceAbstract
     */
    public static function factory($type, $options = null)
    {
        if (!is_string($type) || !$type) {
            Mage::throwException(__('The adapter type must be a non empty string.'));
        }
        $adapterClass = 'Magento_ImportExport_Model_Import_Source_' . ucfirst(strtolower($type));

        if (!class_exists($adapterClass)) {
            Mage::throwException("'{$type}' file extension is not supported");
        }
        $adapter = new $adapterClass($options);

        if (! $adapter instanceof Magento_ImportExport_Model_Import_SourceAbstract) {
            Mage::throwException(
                __('Adapter must be an instance of Magento_ImportExport_Model_Import_SourceAbstract')
            );
        }
        return $adapter;
    }

    /**
     * Create adapter instance for specified source file.
     *
     * @param string $source Source file path.
     * @return Magento_ImportExport_Model_Import_SourceAbstract
     */
    public static function findAdapterFor($source)
    {
        return self::factory(pathinfo($source, PATHINFO_EXTENSION), $source);
    }
}
