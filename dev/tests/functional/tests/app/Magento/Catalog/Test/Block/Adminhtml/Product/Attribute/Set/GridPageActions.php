<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set;

use Magento\Backend\Test\Block\GridPageActions as AbstractGridPageActions;

/**
 * Class GridPageActions
 * Grid page actions block on CatalogProductSetAdd page
 */
class GridPageActions extends AbstractGridPageActions
{
    /**
     * "Add New" button
     *
     * @var string
     */
    protected $addNewButton = '[data-ui-id="page-actions-toolbar-addbutton"]';
}
