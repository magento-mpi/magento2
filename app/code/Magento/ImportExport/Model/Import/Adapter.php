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
namespace Magento\ImportExport\Model\Import;

class Adapter
{
    /**
     * Adapter factory. Checks for availability, loads and create instance of import adapter object.
     *
     * @param string $type Adapter type ('csv', 'xml' etc.)
     * @param \Magento\Filesystem $filesystem
     * @param mixed $options OPTIONAL Adapter constructor options
     * @throws \Exception
     * @return \Magento\ImportExport\Model\Import\AbstractSource
     */
    public static function factory($type, $filesystem, $options = null)
    {
        if (!is_string($type) || !$type) {
            throw new \Magento\Core\Exception(__('The adapter type must be a non empty string.'));
        }
        $adapterClass = 'Magento\ImportExport\Model\Import\Source\\' . ucfirst(strtolower($type));

        if (!class_exists($adapterClass)) {
            throw new \Magento\Core\Exception("'{$type}' file extension is not supported");
        }
        $adapter = new $adapterClass($options, $filesystem);

        if (! $adapter instanceof \Magento\ImportExport\Model\Import\AbstractSource) {
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
     * @param \Magento\Filesystem $filesystem
     * @return \Magento\ImportExport\Model\Import\AbstractSource
     */
    public static function findAdapterFor($source, $filesystem)
    {
        return self::factory(pathinfo($source, PATHINFO_EXTENSION), $filesystem, $source);
    }
}
