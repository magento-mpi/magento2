<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit form for Catalog product and category URL rewrites
 *
 * @method Mage_Catalog_Model_Product getProduct()
 * @method Mage_Catalog_Model_Category getCategory()
 * @method Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form setProduct(Mage_Catalog_Model_Product $product)
 * @method Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form setCategory(Mage_Catalog_Model_Category $category)
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form extends Mage_Adminhtml_Block_Urlrewrite_Edit_Form
{
    /**
     * Form post init
     *
     * @param Varien_Data_Form $form
     * @return Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form
     */
    protected function _formPostInit($form)
    {
        // Set form action
        $form->setAction(
            Mage::helper('Mage_Adminhtml_Helper_Data')->getUrl('*/*/save', array(
                'id'       => $this->_getModel()->getId(),
                'product'  => $this->_getProduct()->getId(),
                'category' => $this->_getCategory()->getId()
            ))
        );

        // Fill id path, request path and target path elements
        /** @var $idPath Varien_Data_Form_Element_Abstract */
        $idPath = $this->getForm()->getElement('id_path');
        /** @var $requestPath Varien_Data_Form_Element_Abstract */
        $requestPath = $this->getForm()->getElement('request_path');
        /** @var $targetPath Varien_Data_Form_Element_Abstract */
        $targetPath = $this->getForm()->getElement('target_path');

        $model = $this->_getModel();
        $disablePaths = false;
        if (!$model->getId()) {
            $product = null;
            $category = null;
            if ($this->_getProduct()->getId()) {
                $product = $this->_getProduct();
                $category = $this->_getCategory();
            } elseif ($this->_getCategory()->getId()) {
                $category = $this->_getCategory();
            }

            if ($product || $category) {
                /** @var $catalogUrlModel Mage_Catalog_Model_Url */
                $catalogUrlModel = Mage::getSingleton('Mage_Catalog_Model_Url');
                $idPath->setValue($catalogUrlModel->generatePath('id', $product, $category));

                $sessionData = $this->_getSessionData();
                if (!isset($sessionData['request_path'])) {
                    $requestPath->setValue($catalogUrlModel->generatePath('request', $product, $category, ''));
                }
                $targetPath->setValue($catalogUrlModel->generatePath('target', $product, $category));
                $disablePaths = true;
            }
        } else {
            $disablePaths = $model->getProductId() || $model->getCategoryId();
        }

        // Disable id_path and target_path elements
        if ($disablePaths) {
            $idPath->setData('disabled', true);
            $targetPath->setData('disabled', true);
        }

        return $this;
    }

    /**
     * Get catalog entity associated stores
     *
     * @return array
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getEntityStores()
    {
        $product = $this->_getProduct();
        $category = $this->_getCategory();
        $entityStores = array();

        // showing websites that only associated to products
        if ($product->getId()) {
            $entityStores = (array) $product->getStoreIds();

            //if category is chosen, reset stores which are not related with this category
            if ($category->getId()) {
                $categoryStores = (array) $category->getStoreIds();
                $entityStores = array_intersect($entityStores, $categoryStores);
            }
            // @codingStandardsIgnoreStart
            if (!$entityStores) {
                throw new Mage_Core_Model_Store_Exception(
                    __('We can\'t set up a URL rewrite because the product you chose is not associated with a website.')
                );
            }
            $this->_requireStoresFilter = true;
        } elseif ($category->getId()) {
            $entityStores = (array) $category->getStoreIds();
            if (!$entityStores) {
                throw new Mage_Core_Model_Store_Exception(
                    __('We can\'t set up a URL rewrite because the category your chose is not associated with a website.')
                );
            }
            $this->_requireStoresFilter = true;
        }
        // @codingStandardsIgnoreEnd

        return $entityStores;
    }

    /**
     * Get product model instance
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setProduct(Mage::getModel('Mage_Catalog_Model_Product'));
        }
        return $this->getProduct();
    }

    /**
     * Get category model instance
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCategory()
    {
        if (!$this->hasData('category')) {
            $this->setCategory(Mage::getModel('Mage_Catalog_Model_Category'));
        }
        return $this->getCategory();
    }
}
