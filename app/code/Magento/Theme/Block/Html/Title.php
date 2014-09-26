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
 *
 * @method $this setTitleId($titleId)
 * @method $this setTitleClass($titleClass)
 * @method $this setTitlePrefix($titlePrefix)
 * @method $this setTitleSuffix($titleSuffix)
 * @method string getTitleId()
 * @method string getTitleClass()
 * @method string getTitlePrefix()
 * @method string getTitleSuffix()
 */
class Title extends \Magento\Framework\View\Element\Template
{
    /**
     * Own page title to display on the page
     *
     * @var string
     */
    protected $pageTitle;

    /**
     * Provide own page title or pick it from Head Block
     *
     * @return string
     */
    public function getPageTitle()
    {
        if (!empty($this->pageTitle)) {
            return $this->pageTitle;
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
        $this->pageTitle = $pageTitle;
    }
}
