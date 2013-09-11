<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category controller
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Controller;

class Category extends \Magento\Core\Controller\Front\Action
{
    /**
     * Initialize requested category object
     *
     * @return \Magento\Catalog\Model\Category
     */
    protected function _initCategory()
    {
        $this->_eventManager->dispatch('catalog_controller_category_init_before', array('controller_action' => $this));
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            return false;
        }

        $category = \Mage::getModel('Magento\Catalog\Model\Category')
            ->setStoreId(\Mage::app()->getStore()->getId())
            ->load($categoryId);

        if (!\Mage::helper('Magento\Catalog\Helper\Category')->canShow($category)) {
            return false;
        }
        \Mage::getSingleton('Magento\Catalog\Model\Session')->setLastVisitedCategoryId($category->getId());
        \Mage::register('current_category', $category);
        try {
            $this->_eventManager->dispatch(
                'catalog_controller_category_init_after',
                array(
                    'category' => $category,
                    'controller_action' => $this
                )
            );
        } catch (\Magento\Core\Exception $e) {
            \Mage::logException($e);
            return false;
        }

        return $category;
    }

    /**
     * Category view action
     */
    public function viewAction()
    {
        $category = $this->_initCategory();
        if ($category) {
            $design = \Mage::getSingleton('Magento\Catalog\Model\Design');
            $settings = $design->getDesignSettings($category);

            // apply custom design
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            \Mage::getSingleton('Magento\Catalog\Model\Session')->setLastViewedCategoryId($category->getId());

            $update = $this->getLayout()->getUpdate();
            if ($category->getIsAnchor()) {
                $type = $category->hasChildren() ? 'layered' : 'layered_without_children';
            } else {
                $type = $category->hasChildren() ? 'default' : 'default_without_children';
            }
            $this->addPageLayoutHandles(array('type' => $type, 'id' => $category->getId()));
            $this->loadLayoutUpdates();

            // apply custom layout update once layout is loaded
            $layoutUpdates = $settings->getLayoutUpdates();
            if ($layoutUpdates && is_array($layoutUpdates)) {
                foreach ($layoutUpdates as $layoutUpdate) {
                    $update->addUpdate($layoutUpdate);
                }
            }

            $this->generateLayoutXml()->generateLayoutBlocks();
            // apply custom layout (page) template once the blocks are generated
            if ($settings->getPageLayout()) {
                $this->getLayout()->helper('Magento\Page\Helper\Layout')->applyTemplate($settings->getPageLayout());
            }

            $root = $this->getLayout()->getBlock('root');
            if ($root) {
                $root->addBodyClass('categorypath-' . $category->getUrlPath())
                    ->addBodyClass('category-' . $category->getUrlKey());
            }

            $this->_initLayoutMessages('Magento\Catalog\Model\Session');
            $this->_initLayoutMessages('Magento\Checkout\Model\Session');
            $this->renderLayout();
        } elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }
}
