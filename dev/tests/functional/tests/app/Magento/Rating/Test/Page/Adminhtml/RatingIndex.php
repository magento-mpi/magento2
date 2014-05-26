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
 * Class RatingIndex
 */
class RatingIndex extends BackendPage
{
    const MCA = 'review/rating';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'ratingGrid' => [
            'name' => 'ratingGrid',
            'class' => 'Magento\Rating\Test\Block\Adminhtml\RatingGrid',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\Rating\Test\Block\Adminhtml\RatingGrid
     */
    public function getRatingGrid()
    {
        return $this->getBlockInstance('ratingGrid');
    }
}
