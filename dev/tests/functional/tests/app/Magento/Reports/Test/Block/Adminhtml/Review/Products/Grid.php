<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Review\Products;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class Grid
 * Product Reviews Report grid
 */
class Grid extends AbstractGrid
{
    /**
     * Search product reviews report row selector
     *
     * @var string
     */
    protected $searchRow = '//tr[td[contains(.,"%s")]]';

    /**
     * Open product review report
     *
     * @param string $name
     * @return void
     */
    public function openReview($name)
    {
        $this->_rootElement->find(sprintf($this->searchRow, $name), Locator::SELECTOR_XPATH)->click();
    }
}
