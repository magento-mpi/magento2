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

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Review\Test\Block\Adminhtml\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'reviewForm' => [
            'name' => 'reviewForm',
            'class' => 'Magento\Review\Test\Block\Adminhtml\Edit',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Review\Test\Block\Adminhtml\GridPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\Review\Test\Block\Adminhtml\Edit
     */
    public function getReviewForm()
    {
        return $this->getBlockInstance('reviewForm');
    }
}
