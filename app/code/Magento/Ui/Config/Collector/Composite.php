<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Config\Collector;

use Magento\Framework\View\File\FileList\Factory;

/**
 * Class Composite
 */
class Composite implements CollectorInterface
{
    /**
     * File list factory
     *
     * @var Factory
     */
    protected $fileListFactory;

    /**
     * Base files
     *
     * @var CollectorInterface
     */
    protected $baseFiles;

    /**
     * Theme files
     *
     * @var CollectorInterface
     */
    protected $themeFiles;

    /**
     * Constructor
     *
     * @param Factory $fileListFactory
     * @param Base $baseFiles
     * @param Theme $themeFiles
     */
    public function __construct(Factory $fileListFactory, Base $baseFiles, Theme $themeFiles)
    {
        $this->fileListFactory = $fileListFactory;
        $this->baseFiles = $baseFiles;
        $this->themeFiles = $themeFiles;
    }

    /**
     * Retrieve files
     * Aggregate configuration files from all appropriate locations
     *
     * @param string $filePath
     * @return \Magento\Framework\View\File[]
     */
    public function getFiles($filePath)
    {
        $list = $this->fileListFactory->create();
        $list->add($this->baseFiles->getFiles($filePath));
        $list->add($this->themeFiles->getFiles($filePath));

        return $list->getAll();
    }
}
