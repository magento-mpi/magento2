<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element;

use Magento\View\Element;

class Remove extends Container implements Element
{
    /**
     * Element type
     */
    const TYPE = 'remove';

    /**
     * @param Element $parent
     */
    public function register(Element $parent = null)
    {
        $this->removeElement($this->meta['name']);
    }
}
