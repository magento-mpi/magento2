<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document\Scheme;

use Magento\Framework\View\File\CollectorInterface;
use Magento\Doc\Document\Scheme\Collector\Base;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\File\FileList\Factory;

/**
 * Class Collector
 * @package Magento\Doc\Document\Scheme
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
     * @var Base
     */
    protected $baseFiles;

    /**
     * Constructor
     *
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
