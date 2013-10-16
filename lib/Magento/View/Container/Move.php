<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Container;

use Magento\View\Container as ContainerInterface;

class Move extends Base implements ContainerInterface
{
    /**
     * Container type
     */
    const TYPE = 'move';

    /**
     * @param ContainerInterface $parent
     */
    public function register(ContainerInterface $parent = null)
    {
        $element = $this->getElement($this->meta['element']);
        if ($element) {
            $parent = $element->getParentElement();
            if ($parent) {
                $parent->detach($element);
            }

            $destination = $this->getElement($this->meta['destination']);
            if ($destination) {
                $destination->attach($element, $this->alias, $this->before, $this->after);
            }
        }
    }
}
