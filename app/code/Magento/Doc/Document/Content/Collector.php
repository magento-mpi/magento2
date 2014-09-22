<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document\Content;

use Magento\Doc\Document\Content\Collector\Base;

use Magento\Framework\View\File\CollectorInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\File\FileList\Factory;

/**
 * Class Collector
 * @package Magento\Doc\Document\Content
 */
class Collector implements CollectorInterface
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
     * @param Factory $fileListFactory
     * @param Base $baseFiles
     */
    public function __construct(
        Factory $fileListFactory,
        Base $baseFiles
    ) {
        $this->fileListFactory = $fileListFactory;
        $this->baseFiles = $baseFiles;
    }

    /**
     * Retrieve files
     *
     * Aggregate layout files from modules and a theme and its ancestors
     *
     * @param ThemeInterface $theme
     * @param string $filePath
     * @return \Magento\Framework\View\File[]
     */
    public function getFiles(ThemeInterface $theme, $filePath)
    {
        $list = $this->fileListFactory->create();
        $list->add($this->baseFiles->getFiles($theme, $filePath));
        return $list->getAll();
    }
}
