<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\AdminHtml\Page;

use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Backend Cms Page grid
 *
 * @package Magento\Cms\Test\Block\AdminHtml\Page
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * 'Add New' cms page button
     *
     * @var string
     */
    protected $addNewCmsPage = "//button[@id='add']";

    /**
     * 'Preview' cms page link
     *
     * @var string
     */
    protected $previewCmsPage = "//a[contains(text(),'Preview')]";

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = array(
        'title' => array(
            'selector' => '#cmsPageGrid_filter_title'
        ),
    );

    /**
     * Add new page
     *
     * @return void
     */
    public function addNewCmsPage()
    {
        $this->_rootElement->find($this->addNewCmsPage, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Preview page
     *
     * @return void
     */
    public function previewCmsPage()
    {
        $this->_rootElement->find($this->previewCmsPage, Locator::SELECTOR_XPATH)->click();
    }
}
