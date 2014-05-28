<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Adminhtml; 

use Mtf\Page\BackendPage; 

/**
 * Class CatalogCategoryIndex
 * Category page on the Backend
 */
class CatalogCategoryIndex extends BackendPage
{
    const MCA = 'catalog/category/index/index'; // TODO: Fix after resolving issue with factory page generation

    protected $_blocks = [
        'treeCategories' => [
            'name' => 'treeCategories',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Category\Tree',
            'locator' => '[id="page:left"]',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'editForm' => [
            'name' => 'editForm',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Category\Edit\Form',
            'locator' => '#category-edit-container',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Category\Tree
     */
    public function getTreeCategories()
    {
        return $this->getBlockInstance('treeCategories');
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
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Category\Edit\Form
     */
    public function getEditForm()
    {
        return $this->getBlockInstance('editForm');
    }
}
