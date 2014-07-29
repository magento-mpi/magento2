<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class CustomerGrid
 * Backend customer grid
 *
 */
class CustomerGrid extends AbstractGrid
{
    /**
     * CSS selector grid mass action form
     *
     * @var string
     */
    protected $gridActionBlock = '#customerGrid_massaction';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => '#customerGrid_filter_name',
        ],
        'email' => [
            'selector' => '#customerGrid_filter_email',
        ],
        'group' => [
            'selector' => '#customerGrid_filter_group',
            'input' => 'select',
        ],
    ];

    /**
     * Getting grid action form
     *
     * @return \Magento\Customer\Test\Block\Adminhtml\Grid\Massaction
     */
    public function getGridActions()
    {
        return $this->blockFactory->create(
            'Magento\Customer\Test\Block\Adminhtml\Grid\Massaction',
            ['element' => $this->_rootElement->find($this->gridActionBlock)]
        );
    }
}
