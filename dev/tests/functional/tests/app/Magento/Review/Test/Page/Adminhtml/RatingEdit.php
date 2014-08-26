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
 * Class RatingEdit
 */
class RatingEdit extends BackendPage
{
    const MCA = 'review/rating/edit';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageActions' => [
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'ratingForm' => [
            'class' => 'Magento\Review\Test\Block\Adminhtml\Rating\Edit\RatingForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\Review\Test\Block\Adminhtml\Rating\Edit\RatingForm
     */
    public function getRatingForm()
    {
        return $this->getBlockInstance('ratingForm');
    }
}
