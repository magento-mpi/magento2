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

/**
 * Plugin for cache flushing from admin panel
 */
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
     * @param \Magento\View\Asset\MergeService $subject
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCleanMergedJsCss(\Magento\View\Asset\MergeService $subject)
    {
        $this->importStorage->clearMaps();
    }
}
