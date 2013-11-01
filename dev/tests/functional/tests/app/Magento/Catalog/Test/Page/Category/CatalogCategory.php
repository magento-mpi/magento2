<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Category;

use Mtf\Fixture;
use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Core\Test\Block\Messages;
use Magento\Backend\Test\Block\Template;
use Magento\Backend\Test\Block\Catalog\Category\Tree;
use Magento\Backend\Test\Block\Catalog\Category\Edit\Form;

/**
 * Class CatalogCategory
 * Categories page
 *
 * @package Magento\Catalog\Test\Page\Category
 */
class CatalogCategory extends Page
{
    /**
     * URL for category page
     */
    const MCA = 'catalog/category';

    /**
     * Category Edit Form on the Backend
     *
     * @var Form
     */
    private $formBlock;

    /**
     * Categories tree block
     *
     * @var Tree
     */
    private $treeBlock;

    /**
     * Backend abstract block
     *
     * @var Template
     */
    private $templateBlock;

    /**
     * Get messages block
     *
     * @var Messages
     */
    private $messageBlock;

    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        //Blocks
        $this->formBlock = Factory::getBlockFactory()->getMagentoBackendCatalogCategoryEditForm(
            $this->_browser->find('category-edit-container', Locator::SELECTOR_ID));
        $this->templateBlock = Factory::getBlockFactory()->getMagentoBackendTemplate(
            $this->_browser->find('#html-body', Locator::SELECTOR_CSS));
        $this->treeBlock = Factory::getBlockFactory()->getMagentoBackendCatalogCategoryTree(
            $this->_browser->find('.categories-side-col', Locator::SELECTOR_CSS, 'tree'), $this->getTemplateBlock());
        $this->messageBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages', Locator::SELECTOR_CSS));
    }

    /**
     * Get Category edit form
     *
     * @return \Magento\Backend\Test\Block\Catalog\Category\Edit\Form
     */
    public function getFormBlock()
    {
        return $this->formBlock;
    }

    /**
     * Category Tree container on the Backend
     *
     * @return \Magento\Backend\Test\Block\Catalog\Category\Tree
     */
    public function getTreeBlock()
    {
        return $this->treeBlock;
    }

    /**
     * Get abstract block
     *
     * @return \Magento\Backend\Test\Block\Template
     */
    public function getTemplateBlock()
    {
        return $this->templateBlock;
    }

    /**
     * Get messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return $this->messageBlock;
    }

    /**
     * Open page using browser and waiting until loader will be disappeared
     *
     * @param array $params
     *
     * @return $this
     */
    public function open(array $params = array())
    {
        parent::open();
        $this->getTemplateBlock()->waitLoader();
    }
}
