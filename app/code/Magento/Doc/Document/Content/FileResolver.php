<?php
/**
 * Application config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document\Content;

use Magento\Framework\View\DesignInterface;

/**
 * Class FileResolver
 * @package Magento\Doc\Document\Content
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
     * Constructor
     *
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
     * Retrieve the list of document item content files with given name
     *
     * @param string $filename
     * @return \Magento\Framework\View\File[]
     */
    public function get($filename)
    {

        $result = $this->collector->getFiles($this->theme, $filename);
        return $result;
    }
}
