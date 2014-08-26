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
 */
class NewIndex extends BackendPage
{
    const MCA = 'admin/giftcardaccount/new/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageMainForm' => [
            'class' => 'Magento\GiftCardAccount\Test\Block\Adminhtml\Giftcardaccount\Edit\GiftCardAccountForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'pageMainActions' => [
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
