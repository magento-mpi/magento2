<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rating\Test\Page\Adminhtml; 

use Mtf\Page\BackendPage; 

/**
 * Class RatingNew
 */
class RatingNew extends BackendPage
{
    const MCA = 'review/rating/new';

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'ratingForm' => [
            'name' => 'ratingForm',
            'class' => 'Magento\Rating\Test\Block\Adminhtml\Edit\Form',
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
     * @return \Magento\Rating\Test\Block\Adminhtml\Edit\Form
     */
    public function getRatingForm()
    {
        return $this->getBlockInstance('ratingForm');
    }
}
