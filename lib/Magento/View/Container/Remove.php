<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Container;

use Magento\View\Container as ContainerInterface;

class Remove extends Container implements ContainerInterface
{
    /**
     * Container type
     */
    const TYPE = 'remove';

    /**
     * @param ContainerInterface $parent
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function register(ContainerInterface $parent = null)
    {
        $this->removeElement($this->meta['name']);
    }
}
