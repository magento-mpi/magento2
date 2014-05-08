<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class UrlRewriteEditCategory
 */
class UrlRewriteEditCategory extends BackendPage
{
    const MCA = 'admin/urlrewrite/edit/category';

    protected $_blocks = [
        'treeBlock' => [
            'name' => 'treeBlock',
            'class' => 'Magento\Backend\Test\Block\Urlrewrite\Catalog\Category\Tree',
            'locator' => '[data-ui-id="category-selector"]',
            'strategy' => 'css selector',
        ],
        'formBlock' => [
            'name' => 'formBlock',
            'class' => 'Magento\Backend\Test\Block\Urlrewrite\Catalog\Edit\Form',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
            'strategy' => 'css selector',
        ],
        'buttonBlock' => [
            'name' => 'buttonBlock',
            'class' => 'Magento\Backend\Test\Block\Widget\Form',
            'locator' => '#messages .messages',
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
     * @return \Magento\Backend\Test\Block\Urlrewrite\Catalog\Category\Tree
     */
    public function getTreeBlock()
    {
        return $this->getBlockInstance('treeBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\Urlrewrite\Catalog\Edit\Form
     */
    public function getFormBlock()
    {
        return $this->getBlockInstance('formBlock');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\Widget\Form
     */
    public function getButtonBlock()
    {
        return $this->getBlockInstance('buttonBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageMainActions()
    {
        return $this->getBlockInstance('pageMainActions');
    }
}
