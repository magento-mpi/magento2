<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Page\Adminhtml; 

use Mtf\Page\BackendPage; 

/**
 * Class NewsletterSubscriber
 *
 * @package Magento\Customer\Test\Page\Adminhtml
 */
class NewsletterSubscriber extends BackendPage
{
    const MCA = 'newsletter/subscriber';

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
