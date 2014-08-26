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
 * Class CustomerGroupNew
 */
class CustomerGroupNew extends BackendPage
{
    const MCA = 'customer/group/new';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageMainActions' => [
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'pageMainForm' => [
            'class' => 'Magento\Customer\Test\Block\Adminhtml\Group\Edit\Form',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageMainActions()
    {
        return $this->getBlockInstance('pageMainActions');
    }

    /**
     * @return \Magento\Customer\Test\Block\Adminhtml\Group\Edit\Form
     */
    public function getPageMainForm()
    {
        return $this->getBlockInstance('pageMainForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
