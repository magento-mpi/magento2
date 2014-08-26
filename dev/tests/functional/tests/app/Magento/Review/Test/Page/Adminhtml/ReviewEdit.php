<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class ReviewEdit
 */
class ReviewEdit extends BackendPage
{
    const MCA = 'review/product/edit';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageActions' => [
            'class' => 'Magento\Review\Test\Block\Adminhtml\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'reviewForm' => [
            'class' => 'Magento\Review\Test\Block\Adminhtml\ReviewForm',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
        'productGrid' => [
            'class' => 'Magento\Review\Test\Block\Adminhtml\Product\Grid',
            'locator' => '#productGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Review\Test\Block\Adminhtml\FormPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\Review\Test\Block\Adminhtml\ReviewForm
     */
    public function getReviewForm()
    {
        return $this->getBlockInstance('reviewForm');
    }

    /**
     * @return \Magento\Review\Test\Block\Adminhtml\Product\Grid
     */
    public function getProductGrid()
    {
        return $this->getBlockInstance('productGrid');
    }
}
