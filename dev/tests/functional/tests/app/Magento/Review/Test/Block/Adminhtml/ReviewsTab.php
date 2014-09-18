<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Reviews
 * Reviews tab on backend
 */
class ReviewsTab extends Tab
{
    /**
     * Product reviews block selector
     *
     * @var string
     */
    protected $reviews = '#Product_Reviews';

    /**
     * Returns product reviews grid
     *
     * @return \Magento\Review\Test\Block\Adminhtml\Grid
     */
    public function getReviewsGrid()
    {
        return $this->blockFactory->create(
            'Magento\Review\Test\Block\Adminhtml\Grid',
            ['element' => $this->_rootElement->find($this->reviews)]
        );
    }
}
