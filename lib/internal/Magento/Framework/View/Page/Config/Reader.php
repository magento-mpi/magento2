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
    const CSS = 'css';

    const SCRIPT = 'script';

    const LINK = 'link';

    const REMOVE = 'remove';

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
    public function read($headElement)
    {
        /** @var \Magento\Framework\View\Layout\Element $element */
        foreach ($headElement as $element) {
            switch ($element->getName()) {
                case self::CSS:
                case self::SCRIPT:
                case self::LINK:
                    $this->structure->addAssets($element->getAttribute('src'), $this->getAttributes($element));
                    break;

                case self::REMOVE:
                    $this->structure->removeAssets($element->getAttribute('src'));
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
