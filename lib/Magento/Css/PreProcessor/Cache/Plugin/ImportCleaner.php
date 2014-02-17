<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Plugin;

use Magento\App\Filesystem;
use Magento\Css\PreProcessor\Cache\Import\Map\Storage;

class ImportCleaner
{
    /**
     * @var Storage
     */
    protected $importStorage;

    /**
     * @param Storage $importStorage
     */
    public function __construct(
        Storage $importStorage
    ) {
        $this->importStorage = $importStorage;
    }

    /**
     * @param array $arguments
     * @return array
     */
    public function beforeCleanMergedJsCss(array $arguments)
    {
        $this->importStorage->clearImportCache();
        return $arguments;
    }
}
