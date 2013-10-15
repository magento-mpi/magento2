<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element;

use Magento\View\Element;

class Move extends Base implements Element
{
    /**
     * Element type
     */
    const TYPE = 'move';

    /**
     * @param Element $parent
     */
    public function register(Element $parent = null)
    {
        $element = $this->getElement($this->meta['element']);
        if ($element) {
            $parent = $element->getParentElement();
            if ($parent) {
                $parent->detach($element);
            }

            $destination = $this->getElement($this->meta['destination']);
            if ($destination) {
                $destination->attach($element, $this->getAlias(), $this->before, $this->after);
            }
        }
    }
}
