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

class Html implements Layout\ReaderInterface
{
    /**#@+
     * Supported types
     */
    const TYPE_HTML = 'html';
    /**#@-*/

    /**#@+
     * Supported html elements
     */
    const HTML_ATTRIBUTE = 'attribute';
    /**#@-*/

    /**
     * @return string[]
     */
    public function getSupportedNodes()
    {
        return [self::TYPE_HTML];
    }

    /**
     * Process Html structure
     *
     * @param Layout\Reader\Context $readerContext
     * @param Layout\Element $htmlElement
     * @param Layout\Element $parentElement
     * @return $this
     */
    public function process(
        Layout\Reader\Context $readerContext,
        Layout\Element $htmlElement,
        Layout\Element $parentElement
    ) {
        /** @var \Magento\Framework\View\Layout\Element $element */
        foreach ($htmlElement as $element) {
            switch ($element->getName()) {
                case self::HTML_ATTRIBUTE:
                    $$readerContext->getPageConfigStructure()->setElementAttribute(
                        PageConfig::ELEMENT_TYPE_HTML,
                        $element->getAttribute('name'),
                        $element->getAttribute('value')
                    );
                    break;

                default:
                    break;
            }
        }
        return $this;
    }
}
