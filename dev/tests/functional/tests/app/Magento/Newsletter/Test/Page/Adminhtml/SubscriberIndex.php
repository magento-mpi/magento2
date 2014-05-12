<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\Page\Adminhtml; 

use Mtf\Page\BackendPage; 

/**
 * Class SubscriberIndex
 *
 */
class SubscriberIndex extends BackendPage
{
    const MCA = 'newsletter/subscriber/index';

    protected $_blocks = [
        'subscriberGrid' => [
            'name' => 'subscriberGrid',
            'class' => 'Magento\Newsletter\Test\Block\Adminhtml\Subscriber\Grid',
            'locator' => '#subscriberGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Newsletter\Test\Block\Adminhtml\Subscriber\Grid
     */
    public function getSubscriberGrid()
    {
        return $this->getBlockInstance('subscriberGrid');
    }
}
