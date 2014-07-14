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
 * CustomerSegment backend edit page
 */
class CustomerSegmentNew extends BackendPage
{
    const MCA = 'customersegment/index/new/index';

    protected $_blocks = [
        'customerSegmentForm' => [
            'name' => 'customerSegmentForm',
            'class' => 'Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Edit\CustomerSegmentForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'pageMainActions' => [
            'name' => 'pageMainActions',
            'class' => 'Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Edit\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Edit\CustomerSegmentForm
     */
    public function getFormTabs()
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
}
