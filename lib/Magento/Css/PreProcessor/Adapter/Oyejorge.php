<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Adapter;

/**
 * Oyejorge adapter model
 */
class Oyejorge implements \Magento\Css\PreProcessor\AdapterInterface
{
    /**
     * @var \Magento\Css\PreProcessor\LibraryLoader\Oyejorge
     */
    protected $loader;

    /**
     * @param \Magento\Css\PreProcessor\LibraryLoader\Oyejorge $loader
     */
    public function __construct(
        \Magento\Css\PreProcessor\LibraryLoader\Oyejorge $loader
    ) {
        $this->loader = $loader;
        $this->loader->load();
    }

    /**
     * @param string $sourceFilePath
     * @return string
     */
    public function process($sourceFilePath)
    {
        $parser = new \Less_Parser();
        $parser->parseFile($sourceFilePath, '');
        return $parser->getCss();
    }
}
