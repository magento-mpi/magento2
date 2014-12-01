<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout;

use Magento\Framework\View\LayoutInterface;

/**
 * Interface BuilderInterface
 */
interface BuilderInterface
{
    /**
     * Build structure
     *
     * @return LayoutInterface
     */
    public function build();
}
