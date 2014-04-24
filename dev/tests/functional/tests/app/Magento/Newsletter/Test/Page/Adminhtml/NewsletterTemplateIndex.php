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
 * Class NewsletterTemplateIndex
 *
 * @package Magento\Newsletter\Test\Page\Adminhtml
 */
class NewsletterTemplateIndex extends BackendPage
{
    const MCA = 'newsletter/template/index';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
            'strategy' => 'css selector',
        ],
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Newsletter\Test\Block\Adminhtml\NewsletterTemplateAction',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'newsletterTemplateGrid' => [
            'name' => 'newsletterTemplateGrid',
            'class' => 'Magento\Newsletter\Test\Block\Adminhtml\NewsletterTemplateGrid',
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
     * @return \Magento\Newsletter\Test\Block\Adminhtml\NewsletterTemplateAction
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\Newsletter\Test\Block\Adminhtml\NewsletterTemplateGrid
     */
    public function getNewsletterTemplateGrid()
    {
        return $this->getBlockInstance('newsletterTemplateGrid');
    }
}
