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
 * Class NewsletterTemplateNewIndex
 *
 * @package Magento\Newsletter\Test\Page\Adminhtml
 */
class NewsletterTemplateNewIndex extends BackendPage
{
    const MCA = 'newsletter/template/new/index';

    protected $_blocks = [
        'pageMainActions' => [
            'name' => 'pageMainActions',
            'class' => 'Magento\Newsletter\Test\Block\Adminhtml\NewsletterTemplateFormActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'pageMainForm' => [
            'name' => 'pageMainForm',
            'class' => 'Magento\Newsletter\Test\Block\Adminhtml\NewsletterTemplateForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'messageBlock' => [
            'name' => 'messageBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Newsletter\Test\Block\Adminhtml\NewsletterTemplateFormActions
     */
    public function getPageMainActions()
    {
        return $this->getBlockInstance('pageMainActions');
    }

    /**
     * @return \Magento\Newsletter\Test\Block\Adminhtml\NewsletterTemplateForm
     *
     */
    public function getPageMainForm()
    {
        return $this->getBlockInstance('pageMainForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return $this->getBlockInstance('messageBlock');
    }
}
