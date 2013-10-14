<?php

namespace Magento\View\Element;

use Magento\View\Element;

class Move extends Base implements Element
{
    const TYPE = 'move';

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
                $alias = isset($this->meta['alias']) ? $this->meta['alias'] : null;
                $before = isset($this->meta['before']) ? $this->meta['before'] : null;
                $after = isset($this->meta['after']) ? $this->meta['after'] : null;
                $destination->attach($element, $alias, $before, $after);
            }
        }
    }
}
