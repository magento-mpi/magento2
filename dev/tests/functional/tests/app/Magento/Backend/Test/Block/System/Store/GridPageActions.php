<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\System\Store;

use \Magento\Backend\Test\Block\GridPageActions as ParentGridPageActions;

/**
 * Class GridPageActions
 * Grid page actions block in Cms Block grid page
 */
class GridPageActions extends ParentGridPageActions
{
    /**
     * Add Store button
     *
     * @var string
     */
    protected $addStoreButton = '#add_store';

    /**
     * Click on Add Store Button
     *
     * @return void
     */
    public function addStoreView()
    {
        $this->_rootElement->find($this->addStoreButton)->click();
    }
}
