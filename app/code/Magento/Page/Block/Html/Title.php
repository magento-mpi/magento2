<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Template title block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Page\Block\Html;

class Title extends \Magento\Core\Block\Template
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
        return $this->getLayout()->getBlock('head')->getShortTitle();
    }

    /**
     * Set own page title
     *
     * @param $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        $this->_pageTitle = $pageTitle;
    }
}
