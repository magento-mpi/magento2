<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block;

use Mtf\Block\Block;

/**
 * Class Page
 * Cms Page block for the content on the frontend.
 */
class Page extends Block
{
    /**
     * Cms page content class
     *
     * @var string
     */
    protected $cmsPageContentClass = ".column.main";

    /**
     * Cms page title
     *
     * @var string
     */
    protected $cmsPageTitle = ".page-title";

    /**
     * Get page content text
     *
     * @return string
     */
    public function getPageContent()
    {
        return $this->_rootElement->find($this->cmsPageContentClass)->getText();
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getPageTitle()
    {
        return $this->_rootElement->find($this->cmsPageTitle)->getText();
    }
}
