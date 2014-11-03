<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout;

use Magento\Framework\View;

/**
 * Interface BuilderInterface
 */
interface BuilderInterface
{
    /**
     * Build structure
     *
     * @return View\LayoutInterface
     */
    public function build();
}
