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
 * Category edit block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Category_Edit_Form extends Magento_Adminhtml_Block_Catalog_Category_Abstract
{
    /**
     * Additional buttons on category page
     *
     * @var array
     */
    protected $_additionalButtons = array();

    /**
     * @var string
     */
    protected $_template = 'catalog/category/edit/form.phtml';

    public function __construct(Magento_Backend_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addJs('Magento_Adminhtml::catalog/category/edit.js');
        }

        $category = $this->getCategory();
        $categoryId = (int) $category->getId(); // 0 when we create category, otherwise some value for editing category

        $this->setChild('tabs',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Category_Tabs', 'tabs')
        );

        // Save button
        if (!$category->isReadonly()) {
            $this->addChild('save_button', 'Magento_Adminhtml_Block_Widget_Button', array(
                'label'     => __('Save Category'),
                'onclick'   => "categorySubmit('" . $this->getSaveUrl() . "', true)",
                'class' => 'save'
            ));
        }

        // Delete button
        if (!in_array($categoryId, $this->getRootIds()) && $category->isDeleteable()) {
            $this->addChild('delete_button', 'Magento_Adminhtml_Block_Widget_Button', array(
                'label'     => __('Delete Category'),
                'onclick'   => "categoryDelete('" . $this->getUrl('*/*/delete', array('_current' => true)) . "', true, {$categoryId})",
                'class' => 'delete'
            ));
        }

        // Reset button
        if (!$category->isReadonly()) {
            $resetPath = $categoryId ? '*/*/edit' : '*/*/add';
            $this->addChild('reset_button', 'Magento_Adminhtml_Block_Widget_Button', array(
                'label'     => __('Reset'),
                'onclick'   => "categoryReset('".$this->getUrl($resetPath, array('_current'=>true))."',true)"
            ));
        }

        return parent::_prepareLayout();
    }

    public function getStoreConfigurationUrl()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $params = array();
//        $params = array('section'=>'catalog');
        if ($storeId) {
            $store = Mage::app()->getStore($storeId);
            $params['website'] = $store->getWebsite()->getCode();
            $params['store']   = $store->getCode();
        }
        return $this->getUrl('*/system_store', $params);
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getSaveButtonHtml()
    {
        if ($this->hasStoreRootCategory()) {
            return $this->getChildHtml('save_button');
        }
        return '';
    }

    public function getResetButtonHtml()
    {
        if ($this->hasStoreRootCategory()) {
            return $this->getChildHtml('reset_button');
        }
        return '';
    }

    /**
     * Retrieve additional buttons html
     *
     * @return string
     */
    public function getAdditionalButtonsHtml()
    {
        $html = '';
        foreach ($this->_additionalButtons as $childName) {
            $html .= $this->getChildHtml($childName);
        }
        return $html;
    }

    /**
     * Add additional button
     *
     * @param string $alias
     * @param array $config
     * @return Magento_Adminhtml_Block_Catalog_Category_Edit_Form
     */
    public function addAdditionalButton($alias, $config)
    {
        if (isset($config['name'])) {
            $config['element_name'] = $config['name'];
        }
        $this->setChild($alias . '_button',
                        $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')->addData($config));
        $this->_additionalButtons[$alias] = $alias . '_button';
        return $this;
    }

    /**
     * Remove additional button
     *
     * @param string $alias
     * @return Magento_Adminhtml_Block_Catalog_Category_Edit_Form
     */
    public function removeAdditionalButton($alias)
    {
        if (isset($this->_additionalButtons[$alias])) {
            $this->unsetChild($this->_additionalButtons[$alias]);
            unset($this->_additionalButtons[$alias]);
        }

        return $this;
    }

    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    public function getHeader()
    {
        if ($this->hasStoreRootCategory()) {
            if ($this->getCategoryId()) {
                return $this->getCategoryName();
            } else {
                $parentId = (int) $this->getRequest()->getParam('parent');
                if ($parentId && ($parentId != Magento_Catalog_Model_Category::TREE_ROOT_ID)) {
                    return __('New Subcategory');
                } else {
                    return __('New Root Category');
                }
            }
        }
        return __('Set Root Category for Store');
    }

    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }

    /**
     * Return URL for refresh input element 'path' in form
     *
     * @param array $args
     * @return string
     */
    public function getRefreshPathUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/refreshPath', $params);
    }

    public function getProductsJson()
    {
        $products = $this->getCategory()->getProductsPosition();
        if (!empty($products)) {
            return $this->_coreData->jsonEncode($products);
        }
        return '{}';
    }

    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }
}
