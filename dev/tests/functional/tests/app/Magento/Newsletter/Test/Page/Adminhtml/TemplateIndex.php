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
 * Class TemplateIndex
 *
 * @package Magento\Newsletter\Test\Page\Adminhtml
 */
class TemplateIndex extends BackendPage
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
            'class' => 'Magento\Newsletter\Test\Block\Adminhtml\Template\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'newsletterTemplateGrid' => [
            'name' => 'newsletterTemplateGrid',
            'class' => 'Magento\Newsletter\Test\Block\Adminhtml\Template\Grid',
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
     * @return \Magento\Newsletter\Test\Block\Adminhtml\Template\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\Newsletter\Test\Block\Adminhtml\Template\Grid
     */
    public function getNewsletterTemplateGrid()
    {
        return $this->getBlockInstance('newsletterTemplateGrid');
    }
}
