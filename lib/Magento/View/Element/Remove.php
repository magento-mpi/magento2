<?php

namespace Magento\View\Element;

use Magento\View\Element;

class Remove extends Container implements Element
{
    const TYPE = 'remove';

    public function register(Element $parent = null)
    {
        //var_dump($this->meta['name']);
        $this->removeElement($this->meta['name']);
    }
}
