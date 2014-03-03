<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

use Magento\View\Element\BlockInterface;

/**
 * Catalog category widgets controller for CMS WYSIWYG
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Widget extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Chooser Source action
     *
     * @return void
     */
    public function chooserAction()
    {
        $this->getResponse()->setBody(
            $this->_getCategoryTreeBlock()->toHtml()
        );
    }

    /**
     * Categories tree node (Ajax version)
     *
     * @return void
     */
    public function categoriesJsonAction()
    {
        $categoryId = (int)$this->getRequest()->getPost('id');
        if ($categoryId) {

            $category = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
            if ($category->getId()) {
                $this->_coreRegistry->register('category', $category);
                $this->_coreRegistry->register('current_category', $category);
            }
            $this->getResponse()->setBody(
                $this->_getCategoryTreeBlock()->getTreeJson($category)
            );
        }
    }

    /**
     * @return BlockInterface
     */
    protected function _getCategoryTreeBlock()
    {
        return $this->_view->getLayout()->createBlock('Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser', '', array(
            'data' => array(
                'id' => $this->getRequest()->getParam('uniq_id'),
                'use_massaction' => $this->getRequest()->getParam('use_massaction', false)
            )
        ));
    }
}
