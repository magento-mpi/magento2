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
 * Edit form for Catalog product and category URL rewrites
 *
 * @method \Magento\Catalog\Model\Product getProduct()
 * @method \Magento\Catalog\Model\Category getCategory()
 * @method \Magento\Adminhtml\Block\Urlrewrite\Catalog\Edit\Form setProduct(\Magento\Catalog\Model\Product $product)
 * @method \Magento\Adminhtml\Block\Urlrewrite\Catalog\Edit\Form setCategory(\Magento\Catalog\Model\Category $category)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Urlrewrite\Catalog\Edit;

class Form extends \Magento\Adminhtml\Block\Urlrewrite\Edit\Form
{
    /**
     * Form post init
     *
     * @param \Magento\Data\Form $form
     * @return \Magento\Adminhtml\Block\Urlrewrite\Catalog\Edit\Form
     */
    protected function _formPostInit($form)
    {
        // Set form action
        $form->setAction(
            \Mage::helper('Magento\Adminhtml\Helper\Data')->getUrl('*/*/save', array(
                'id'       => $this->_getModel()->getId(),
                'product'  => $this->_getProduct()->getId(),
                'category' => $this->_getCategory()->getId()
            ))
        );

        // Fill id path, request path and target path elements
        /** @var $idPath \Magento\Data\Form\Element\AbstractElement */
        $idPath = $this->getForm()->getElement('id_path');
        /** @var $requestPath \Magento\Data\Form\Element\AbstractElement */
        $requestPath = $this->getForm()->getElement('request_path');
        /** @var $targetPath \Magento\Data\Form\Element\AbstractElement */
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
                /** @var $catalogUrlModel \Magento\Catalog\Model\Url */
                $catalogUrlModel = \Mage::getSingleton('Magento\Catalog\Model\Url');
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
     * @throws \Magento\Core\Model\Store\Exception
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
                throw new \Magento\Core\Model\Store\Exception(
                    __('We can\'t set up a URL rewrite because the product you chose is not associated with a website.')
                );
            }
            $this->_requireStoresFilter = true;
        } elseif ($category->getId()) {
            $entityStores = (array) $category->getStoreIds();
            if (!$entityStores) {
                throw new \Magento\Core\Model\Store\Exception(
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
     * @return \Magento\Catalog\Model\Product
     */
    protected function _getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setProduct(\Mage::getModel('Magento\Catalog\Model\Product'));
        }
        return $this->getProduct();
    }

    /**
     * Get category model instance
     *
     * @return \Magento\Catalog\Model\Category
     */
    protected function _getCategory()
    {
        if (!$this->hasData('category')) {
            $this->setCategory(\Mage::getModel('Magento\Catalog\Model\Category'));
        }
        return $this->getCategory();
    }
}
