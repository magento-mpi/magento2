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
 * Urlrewrites edit form for catalog entities
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form extends Mage_Adminhtml_Block_Urlrewrite_Edit_Form
{
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * @var Mage_Catalog_Model_Category
     */
    protected $_category = null;

    /**
     * Form post init
     *
     * @param Varien_Data_Form $form
     * @return Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form
     */
    protected function _formPostInit($form)
    {
        // Set for action
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
            $product  = null;
            if ($this->_hasProduct()) {
                $product = $this->_getProduct();
            }
            $category = null;
            if ($product || $this->_hasCategory()) {
                $category = $this->_getCategory();
            }

            if ($this->_hasCustomEntity()) {
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
        $hasCategory = $this->_hasCategory();
        if ($this->_hasProduct()) {
            $entityStores = (array) $product->getStoreIds();

            //if category is chosen, reset stores which are not related with this category
            if ($hasCategory) {
                $categoryStores = (array) $category->getStoreIds();
                $entityStores = array_intersect($entityStores, $categoryStores);
            }
            if (!$entityStores) {
                throw new Mage_Core_Model_Store_Exception(
                    Mage::helper('Mage_Adminhtml_Helper_Data')
                        ->__('Chosen product does not associated with any website, so url rewrite is not possible.')
                );
            }
            $this->_requireStoresFilter = true;
        } elseif ($hasCategory) {
            $entityStores = (array) $category->getStoreIds();
            if (!$entityStores) {
                throw new Mage_Core_Model_Store_Exception(
                    Mage::helper('Mage_Adminhtml_Helper_Data')
                        ->__('Chosen category does not associated with any website, so url rewrite is not possible.')
                );
            }
            $this->_requireStoresFilter = true;
        }

        return $entityStores;
    }

    /**
     * Has product entity
     *
     * @return bool
     */
    protected function _hasProduct()
    {
        return $this->_getProduct()->getId() > 0;
    }

    /**
     * Has category entity
     *
     * @return bool
     */
    protected function _hasCategory()
    {
        return $this->_getCategory()->getId() > 0;
    }

    /**
     * Has custom catalog entity
     *
     * @return bool
     */
    protected function _hasCustomEntity()
    {
        return $this->_hasProduct() || $this->_hasCategory();
    }

    /**
     * Get product model instance
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        if (is_null($this->_product)) {
            $this->_product = Mage::registry('current_product');
            if (!$this->_product) {
                $this->_product = Mage::getModel('Mage_Catalog_Model_Product');
            }
        }
        return $this->_product;
    }

    /**
     * Get category model instance
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCategory()
    {
        if (is_null($this->_category)) {
            $this->_category = Mage::registry('current_category');
            if (!$this->_category) {
                $this->_category = Mage::getModel('Mage_Catalog_Model_Category');
            }
        }
        return $this->_category;
    }
}
