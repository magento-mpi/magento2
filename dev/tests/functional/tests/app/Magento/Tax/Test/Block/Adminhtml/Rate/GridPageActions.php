<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Block\Adminhtml\Rate;

use \Magento\Backend\Test\Block\GridPageActions as GridPageActionsInterface;

/**
 * Class GridPageActions
 * Grid page actions block in Tax Rate grid page
 */
class GridPageActions extends GridPageActionsInterface
{
    /**
     * "Add New Tax Rate" button
     *
     * @var string
     */
    protected $addNewButton = '.add-tax-rate';
}
