<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Review\Customer;

use Magento\Customer\Test\Fixture\CustomerInjectable;
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
    protected $searchRow = '//tr[td[contains(.,"%s")]]/td';

    /**
     * Search product reviews report row selector
     *
     * @var string
     */
    protected $colReviewCount = '//tr[td[contains(.,"%s")]]/td[@data-column="review_cnt"]';

    /**
     * Open customer review report
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    public function openReview(CustomerInjectable $customer)
    {
        $customerName = $customer->getFirstName() . ' ' . $customer->getLastName();
        $this->_rootElement->find(sprintf($this->searchRow, $customerName), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get qty review from customer review grid
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
