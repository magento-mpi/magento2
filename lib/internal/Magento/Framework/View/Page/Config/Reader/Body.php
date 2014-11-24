<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Page\Config\Reader;

use Magento\Framework\View\Layout;
use Magento\Framework\View\Page\Config as PageConfig;

class Body implements Layout\ReaderInterface
{
    /**#@+
     * Supported types
     */
    const TYPE_BODY = 'body';
    /**#@-*/

    /**#@+
     * Supported body elements
     */
    const BODY_ATTRIBUTE = 'attribute';
    /**#@-*/

    /**
     * @var \Magento\Framework\View\Layout\Reader\Pool
     */
    protected $readerPool;

    /**
     * @param Layout\Reader\Pool $readerPool
     */
    public function __construct(Layout\Reader\Pool $readerPool)
    {
        $this->readerPool = $readerPool;
    }

    /**
     * @return string[]
     */
    public function getSupportedNodes()
    {
        return [self::TYPE_BODY];
    }

    /**
     * Process Body structure
     *
     * @param Layout\Reader\Context $readerContext
     * @param Layout\Element $bodyElement
     * @param Layout\Element $parentElement
     * @return $this
     */
    public function process(
        Layout\Reader\Context $readerContext,
        Layout\Element $bodyElement,
        Layout\Element $parentElement
    ) {
        /** @var \Magento\Framework\View\Layout\Element $element */
        foreach ($bodyElement as $element) {
            switch ($element->getName()) {
                case self::BODY_ATTRIBUTE:
                    $this->setBodyAttributeTosStructure($readerContext, $element);
                    break;

                default:
                    break;
            }
        }
        return $this->readerPool->readStructure($readerContext, $bodyElement);
    }

    /**
     * @param Layout\Reader\Context $readerContext
     * @param Layout\Element $element
     * @return $this
     */
    protected function setBodyAttributeTosStructure(Layout\Reader\Context $readerContext, Layout\Element $element)
    {
        if ($element->getAttribute('name') == PageConfig::BODY_ATTRIBUTE_CLASS) {
            $readerContext->getPageConfigStructure()->setBodyClass($element->getAttribute('value'));
        } else {
            $readerContext->getPageConfigStructure()->setElementAttribute(
                PageConfig::ELEMENT_TYPE_BODY,
                $element->getAttribute('name'),
                $element->getAttribute('value')
            );
        }
        return $this;
    }
}
