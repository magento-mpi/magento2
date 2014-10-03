<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout;

interface GeneratorInterface
{
    /**
     * Traverse through all elements of specified XML-node and schedule structural elements of it
     *
     * @param Reader\Context $readerContext
     * @param string $elementType
     * @param null $layout
     * @return $this
     */
    public function process(Reader\Context $readerContext, $elementType, $layout = null);

    /**
     * Return type of generator
     *
     * @return string
     */
    public function getType();
}
