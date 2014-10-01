<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout;

interface ReaderInterface
{
    /**
     * Get all supported nodes of this reader
     *
     * @return string[]
     */
    public function getSupportedNodes();

    /**
     * Process all structure
     *
     * @param Reader\Context $readerContext
     * @param Element $element
     * @param Element $parentElement
     * @return $this
     */
    public function process(Reader\Context $readerContext, Element $element, Element $parentElement);
}
