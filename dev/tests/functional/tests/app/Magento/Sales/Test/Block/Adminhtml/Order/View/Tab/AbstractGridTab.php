<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\View\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Abstract Class AbstractGridTab
 * Grid tab on order view page
 */
abstract class AbstractGridTab extends Tab
{
    /**
     * Grid block css selector
     *
     * @var string
     */
    protected $grid;

    /**
     * Class name
     *
     * @var string
     */
    protected $class;

    /**
     * Get grid block
     *
     * @return Grid
     */
    public function getGridBlock()
    {
        return $this->blockFactory->create($this->class, ['element' => $this->_rootElement->find($this->grid)]);
    }
}
