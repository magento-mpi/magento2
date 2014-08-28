<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Reviews
 * Reviews tab on customer edit page.
 */
class Reviews extends Tab
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
     * @return \Magento\Review\Test\Block\Adminhtml\Customer\Edit\Tab\Reviews\ReviewsGrid
     */
    public function getReviewsGrid()
    {
        return $this->blockFactory->create(
            'Magento\Review\Test\Block\Adminhtml\Customer\Edit\Tab\Reviews\ReviewsGrid',
            ['element' => $this->_rootElement->find($this->reviews)]
        );
    }
}
