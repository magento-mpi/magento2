<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class UrlrewriteEdit
 */
class UrlrewriteEdit extends BackendPage
{
    const MCA = 'admin/urlrewrite/edit';

    protected $_blocks = [
        'treeBlock' => [
            'name' => 'treeBlock',
            'class' => 'Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Category\Tree',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'formBlock' => [
            'name' => 'formBlock',
            'class' => 'Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Edit\UrlRewriteForm',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
            'strategy' => 'css selector',
        ],
        'pageMainActions' => [
            'name' => 'pageMainActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'productGridBlock' => [
            'name' => 'productGridBlock',
            'class' => 'Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Product\Grid',
            'locator' => '[id="productGrid"]',
            'strategy' => 'css selector',
        ],
        'urlRewriteTypeSelectorBlock' => [
            'name' => 'urlRewriteTypeSelectorBlock',
            'class' => 'Magento\UrlRewrite\Test\Block\Adminhtml\Selector',
            'locator' => '[data-ui-id="urlrewrite-type-selector"]',
            'strategy' => 'css selector',
        ],
        'cmsGridBlock' => [
            'name' => 'gridBlock',
            'class' => 'Magento\UrlRewrite\Test\Block\Adminhtml\Cms\Page\Grid',
            'locator' => '#cmsPageGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Category\Tree
     */
    public function getTreeBlock()
    {
        return $this->getBlockInstance('treeBlock');
    }

    /**
     * @return \Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Edit\UrlRewriteForm
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
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageMainActions()
    {
        return $this->getBlockInstance('pageMainActions');
    }

    /**
     * @return \Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Product\Grid
     */
    public function getProductGridBlock()
    {
        return $this->getBlockInstance('productGridBlock');
    }

    /**
     * @return \Magento\UrlRewrite\Test\Block\Adminhtml\Selector
     */
    public function getUrlRewriteTypeSelectorBlock()
    {
        return $this->getBlockInstance('urlRewriteTypeSelectorBlock');
    }

    /**
     * @return \Magento\UrlRewrite\Test\Block\Adminhtml\Cms\Page\Grid
     */
    public function getCmsGridBlock()
    {
        return $this->getBlockInstance('cmsGridBlock');
    }
}
