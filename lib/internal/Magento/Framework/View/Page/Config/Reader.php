<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Page\Config;

class Reader
{
    /**#@+
     * Supported head elements
     */
    const HEAD_CSS = 'css';

    const HEAD_SCRIPT = 'script';

    const HEAD_LINK = 'link';

    const HEAD_REMOVE = 'remove';

    const HEAD_TITLE = 'title';

    const HEAD_META = 'meta';
    /**#@-*/

    /**
     * @var Structure
     */
    protected $structure;

    /**
     * @param \Magento\Framework\View\Page\Config\Structure $structure
     */
    public function __construct(Structure $structure)
    {
        $this->structure = $structure;
    }

    /**
     * @param \Magento\Framework\View\Layout\Element $headElement
     * @return $this
     */
    public function readHead($headElement)
    {
        /** @var \Magento\Framework\View\Layout\Element $element */
        foreach ($headElement as $element) {
            switch ($element->getName()) {
                case self::HEAD_CSS:
                case self::HEAD_SCRIPT:
                case self::HEAD_LINK:
                    $this->structure->addAssets($element->getAttribute('src'), $this->getAttributes($element));
                    break;

                case self::HEAD_REMOVE:
                    $this->structure->removeAssets($element->getAttribute('src'));
                    break;

                case self::HEAD_TITLE:
                    $this->structure->setTitle($element);
                    break;

                case self::HEAD_META:
                    $this->structure->setMetaData($element->getAttribute('name'), $element->getAttribute('content'));
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
