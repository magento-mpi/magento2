<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CustomerSegmentNew
 */
class CustomerSegmentNew extends BackendPage
{
    const MCA = 'customersegment/index/new';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'customerSegmentForm' => [
            'class' => 'Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Edit\CustomerSegmentForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'pageMainActions' => [
            'class' => 'Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Edit\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.messages .messages',
            'strategy' => 'css selector',
        ],
        'customerSegmentGrid' => [
            'class' => 'Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\CustomerGrid',
            'locator' => '#segmentGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Edit\CustomerSegmentForm
     */
    public function getCustomerSegmentForm()
    {
        return $this->getBlockInstance('customerSegmentForm');
    }

    /**
     * @return \Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Edit\FormPageActions
     */
    public function getPageMainActions()
    {
        return $this->getBlockInstance('pageMainActions');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\CustomerGrid
     */
    public function getCustomerSegmentGrid()
    {
        return $this->getBlockInstance('customerSegmentGrid');
    }
}
