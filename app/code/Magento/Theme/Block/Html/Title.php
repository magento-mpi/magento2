<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Block\Html;

use Magento\Framework\View\Element\Template;

/**
 * Html page title block
 */
class Title extends \Magento\Framework\View\Element\Template
{
    /**
     * Own page title to display on the page
     *
     * @var string
     */
    protected $_pageTitle;

    /**
     * Provide own page title or pick it from Head Block
     *
     * @return string
     */
    public function getPageTitle()
    {
        if (!empty($this->_pageTitle)) {
            return $this->_pageTitle;
        }
        return $this->pageConfig->getShortTitle();
    }

    /**
     * Set own page title
     *
     * @param string $pageTitle
     * @return void
     */
    public function setPageTitle($pageTitle)
    {
        $this->_pageTitle = $pageTitle;
    }
}
