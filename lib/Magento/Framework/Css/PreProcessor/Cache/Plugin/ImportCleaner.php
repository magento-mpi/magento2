<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Css\PreProcessor\Cache\Plugin;

use Magento\Framework\App\Filesystem;
use Magento\Framework\Css\PreProcessor\Cache\Import\Map\Storage;

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
    public function __construct(Storage $importStorage)
    {
        $this->importStorage = $importStorage;
    }

    /**
     * @param \Magento\Framework\View\Asset\MergeService $subject
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCleanMergedJsCss(\Magento\Framework\View\Asset\MergeService $subject)
    {
        $this->importStorage->clearMaps();
    }
}
