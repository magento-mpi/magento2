<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Review\Customer;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class Grid
 * Customer Report Review grid
 */
class Grid extends AbstractGrid
{
    /**
     * Search product reviews report row selector
     *
     * @var string
     */
    protected $searchRow = '//tr[td[contains(.,"%s")]]/td[contains(.,"Show Reviews")]';

    /**
     * Search product reviews report row selector
     *
     * @var string
     */
    protected $colReviewCount = '//tr[td[contains(.,"%s")]]/td[@data-column="review_cnt"]';

    /**
     * Open product review report
     *
     * @param string $customerName
     * @return void
     */
    public function openReview($customerName)
    {
        $this->_rootElement->find(sprintf($this->searchRow, $customerName), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Open product review report
     *
     * @param string $customerName
     * @return int
     */
    public function getQtyReview($customerName)
    {
        return $this->_rootElement
            ->find(sprintf($this->colReviewCount, $customerName), Locator::SELECTOR_XPATH)->getText();
    }
}
