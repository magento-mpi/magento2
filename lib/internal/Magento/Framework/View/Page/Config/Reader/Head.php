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

class Head implements Layout\ReaderInterface
{
    /**#@+
     * Supported types
     */
    const TYPE_HEAD = 'head';
    /**#@-*/

    /**#@+
     * Supported head elements
     */
    const HEAD_CSS = 'css';

    const HEAD_SCRIPT = 'script';

    const HEAD_LINK = 'link';

    const HEAD_REMOVE = 'remove';

    const HEAD_TITLE = 'title';

    const HEAD_META = 'meta';

    const HEAD_ATTRIBUTE = 'attribute';
    /**#@-*/

    /**
     * @return string[]
     */
    public function getSupportedNodes()
    {
        return [self::TYPE_HEAD];
    }

    /**
     * Process Head structure
     *
     * @param Layout\Reader\Context $readerContext
     * @param Layout\Element $headElement
     * @param Layout\Element $parentElement
     * @return $this
     */
    public function process(
        Layout\Reader\Context $readerContext,
        Layout\Element $headElement,
        Layout\Element $parentElement
    ) {
        /** @var \Magento\Framework\View\Layout\Element $node */
        foreach ($headElement as $node) {
            switch ($node->getName()) {
                case self::HEAD_CSS:
                case self::HEAD_SCRIPT:
                case self::HEAD_LINK:
                    $readerContext->getPageConfigStructure()
                        ->addAssets($node->getAttribute('src'), $this->getAttributes($node));
                    break;

                case self::HEAD_REMOVE:
                    $readerContext->getPageConfigStructure()->removeAssets($node->getAttribute('src'));
                    break;

                case self::HEAD_TITLE:
                    $readerContext->getPageConfigStructure()->setTitle($node);
                    break;

                case self::HEAD_META:
                    $readerContext->getPageConfigStructure()
                        ->setMetaData($node->getAttribute('name'), $node->getAttribute('content'));
                    break;

                case self::HEAD_ATTRIBUTE:
                    $readerContext->getPageConfigStructure()->setElementAttribute(
                        PageConfig::ELEMENT_TYPE_HEAD,
                        $node->getAttribute('name'),
                        $node->getAttribute('value')
                    );
                    break;

                default:
                    break;
            }
        }
        return $this;
    }

    /**
     * @param \Magento\Framework\View\Layout\Element $element
     * @return array
     */
    protected function getAttributes($element)
    {
        $attributes = [];
        foreach ($element->attributes() as $attrName => $attrValue) {
            $attributes[$attrName] = (string)$attrValue;
        }
        return $attributes;
    }
}
