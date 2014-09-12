<?php
/**
 * Application config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document\Outline;

use Magento\Framework\View\DesignInterface;

/**
 * Class FileResolver
 * @package Magento\Doc\Document\Outline
 */
class FileResolver
{
    /**
     * @var \Magento\Core\Model\Theme
     */
    protected $theme;

    /**
     * @var Collector
     */
    protected $collector;

    /**
     * @param DesignInterface $design
     * @param Collector $collector
     */
    public function __construct(
        DesignInterface $design,
        Collector $collector
    ) {
        $this->theme = $design->getDesignTheme();
        $this->collector = $collector;
    }

    /**
     * {@inheritdoc}
     */
    public function get($filename)
    {
        $files = $this->collector->getFiles($this->theme, $filename);
        $result = [];
        foreach ($files as $file) {
            /** @var \Magento\Framework\View\File $file */
            $result[] = file_get_contents($file->getFilename());
        }
        return $result;
    }
}
