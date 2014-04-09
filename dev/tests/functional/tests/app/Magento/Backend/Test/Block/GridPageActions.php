<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block;

/**
 * Class GridPageActions
 * Grid page actions block
 *
 * @package Magento\Backend\Test\Block
 */
class GridPageActions extends PageActions
{
    /**
     * "Add New" button
     *
     * @var string
     */
    protected $addNewButton = '#add';

    /**
     * Click on "Add New" button
     */
    public function addNew()
    {
        $this->_rootElement->find($this->addNewButton)->click();
    }
}
