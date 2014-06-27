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
 * Class ReviewIndex
 */
class ReviewIndex extends BackendPage
{
    const MCA = 'review/product/index';

    protected $_blocks = [
        'reviewGrid' => [
            'name' => 'reviewGrid',
            'class' => 'Magento\Review\Test\Block\Adminhtml\Grid',
            'locator' => '#reviwGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Review\Test\Block\Adminhtml\Grid
     */
    public function getReviewGrid()
    {
        return $this->getBlockInstance('reviewGrid');
    }
}
