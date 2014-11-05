<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Block;

use Mtf\Block\Block;

/**
 * Class Page
 * Cms Page block for the content on the frontend.
 */
class HierarchyNode extends Block
{
    /**
     * Cms menu
     *
     * @var string
     */
    protected $cmsMenu = ".cms-menu";

    /**
     * Check is visible cms menu
     *
     * @return bool
     */
    public function cmsMenuIsVisible()
    {
        return $this->_rootElement->find($this->cmsMenu)->isVisible();
    }
}
