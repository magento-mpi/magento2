<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block;

use Mtf\Block\Block;

/**
 * Class for page actions in backend block
 *
 * @package Magento\Backend\Test\Block
 */
class PageActions extends Block
{
    /**
     * Click "add new" button
     */
    public function clickAddNew()
    {
        $this->_rootElement->find('#add')->click();
    }
}
