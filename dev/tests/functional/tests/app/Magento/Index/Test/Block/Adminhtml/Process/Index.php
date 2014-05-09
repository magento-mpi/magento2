<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Index\Test\Block\Adminhtml\Process;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class Index
 * Index management grid
 *
 */
class Index extends Grid
{
    /**
     * @var string
     */
    protected $actionsDropdown = '#massaction-select';

    /**
     * @var string
     */
    protected $selectAll = './/option[@value="selectAll"]';

    /**
     * Mass action for Reindex Data
     */
    public function reindexAll()
    {
        $this->_rootElement->find($this->actionsDropdown, Locator::SELECTOR_CSS, 'select')->setValue('Select All');
        $this->_rootElement->find($this->massactionSubmit, Locator::SELECTOR_CSS)->click();
    }
}