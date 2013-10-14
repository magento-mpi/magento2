<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Backend;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class PageGrid
 * Backend cms page grid
 *
 * @package Magento\Cms\Test\Block\Backend
 */
class PageGrid extends Grid
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'page_title' => '#cmsPageGrid_filter_title',
        );
    }

    public function deleteAll(array $filter = array())
    {
        $this->searchAndOpen($filter);
        $this->_rootElement->find('delete', Locator::SELECTOR_ID)->click();
        $this->_rootElement->acceptAlert();
    }
}
