<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class NewIndex
 *
 * @package Magento\GiftCardAccount\Test\Page\Adminhtml
 */
class NewIndex extends BackendPage
{
    const MCA = 'admin/giftcardaccount/new/index';

    protected $_blocks = [
        'pageMainForm' => [
            'name' => 'pageMainForm',
            'class' => 'Magento\GiftCardAccount\Test\Block\Adminhtml\Giftcardaccount\Edit\GiftCardAccountForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'pageMainActions' => [
            'name' => 'pageMainActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftCardAccount\Test\Block\Adminhtml\Giftcardaccount\Edit\GiftCardAccountForm
     */
    public function getPageMainForm()
    {
        return $this->getBlockInstance('pageMainForm');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageMainActions()
    {
        return $this->getBlockInstance('pageMainActions');
    }
}
