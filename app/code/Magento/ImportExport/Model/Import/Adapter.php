<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Import;

use Magento\Filesystem\Directory\Write;
use Magento\ImportExport\Model\Import\AbstractSource;

/**
 * Import adapter model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Adapter
{
    /**
     * Adapter factory. Checks for availability, loads and create instance of import adapter object.
     *
     * @param string $type Adapter type ('csv', 'xml' etc.)
     * @param Write $directory
     * @param mixed $options OPTIONAL Adapter constructor options
     * @return AbstractSource
     * @throws \Magento\Core\Exception
     */
    public static function factory($type, $directory, $options = null)
    {
        if (!is_string($type) || !$type) {
            throw new \Magento\Core\Exception(__('The adapter type must be a non empty string.'));
        }
        $adapterClass = 'Magento\ImportExport\Model\Import\Source\\' . ucfirst(strtolower($type));

        if (!class_exists($adapterClass)) {
            throw new \Magento\Core\Exception("'{$type}' file extension is not supported");
        }
        $adapter = new $adapterClass($options, $directory);

        if (! $adapter instanceof AbstractSource) {
            throw new \Magento\Core\Exception(
                __('Adapter must be an instance of \Magento\ImportExport\Model\Import\AbstractSource')
            );
        }
        return $adapter;
    }

    /**
     * Create adapter instance for specified source file.
     *
     * @param string $source Source file path.
     * @param Write $directory
     * @return AbstractSource
     */
    public static function findAdapterFor($source, $directory)
    {
        return self::factory(pathinfo($source, PATHINFO_EXTENSION), $directory, $source);
    }
}
