<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for Catalog Category URL rewrites
 *
 * @method \Magento\Catalog\Model\Category getCategory()
 * @method \Magento\Adminhtml\Block\Urlrewrite\Catalog\Category\Edit
 *    setCategory(\Magento\Catalog\Model\Category $category)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Urlrewrite\Catalog\Category;

class Edit
    extends \Magento\Adminhtml\Block\Urlrewrite\Edit
{
    /**
     * Prepare layout for URL rewrite creating for category
     */
    protected function _prepareLayoutFeatures()
    {
        /** @var $helper \Magento\Adminhtml\Helper\Data */
        $helper = \Mage::helper('Magento\Adminhtml\Helper\Data');

        if ($this->_getUrlRewrite()->getId()) {
            $this->_headerText = __('Edit URL Rewrite for a Category');
        } else {
            $this->_headerText = __('Add URL Rewrite for a Category');
        }

        if ($this->_getCategory()->getId()) {
            $this->_addCategoryLinkBlock();
            $this->_addEditFormBlock();
            $this->_updateBackButtonLink($helper->getUrl('*/*/edit') . 'category');
        } else {
            $this->_addUrlRewriteSelectorBlock();
            $this->_addCategoryTreeBlock();
        }
    }

    /**
     * Get or create new instance of category
     *
     * @return \Magento\Catalog\Model\Product
     */
    private function _getCategory()
    {
        if (!$this->hasData('category')) {
            $this->setCategory(\Mage::getModel('Magento\Catalog\Model\Category'));
        }
        return $this->getCategory();
    }

    /**
     * Add child category link block
     */
    private function _addCategoryLinkBlock()
    {
        /** @var $helper \Magento\Adminhtml\Helper\Data */
        $helper = \Mage::helper('Magento\Adminhtml\Helper\Data');
        $this->addChild('category_link', 'Magento\Adminhtml\Block\Urlrewrite\Link', array(
            'item_url'  => $helper->getUrl('*/*/*') . 'category',
            'item_name' => $this->_getCategory()->getName(),
            'label'     => __('Category:')
        ));
    }

    /**
     * Add child category tree block
     */
    private function _addCategoryTreeBlock()
    {
        $this->addChild('categories_tree', 'Magento\Adminhtml\Block\Urlrewrite\Catalog\Category\Tree');
    }

    /**
     * Creates edit form block
     *
     * @return \Magento\Adminhtml\Block\Urlrewrite\Catalog\Edit\Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock('Magento\Adminhtml\Block\Urlrewrite\Catalog\Edit\Form', '', array(
            'data' => array(
                'category'    => $this->_getCategory(),
                'url_rewrite' => $this->_getUrlRewrite()
            )
        ));
    }
}
