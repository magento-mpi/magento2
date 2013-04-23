<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category controller
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_CategoryController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize requested category object
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCatagory()
    {
        Mage::dispatchEvent('catalog_controller_category_init_before', array('controller_action' => $this));
        $categoryId = (int)$this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            return false;
        }

        $serviceManager = Mage::getSingleton('Mage_Catalog_ServiceManager');
        $category = $serviceManager->getService('catalog_category')
            ->call('initCategoryToView', array(
                'entity_id' => $categoryId,
                'store_id'  => Mage::app()->getStore()->getId(),
                'fields'    => 'entity_id,name,path,is_active,store_id'
            ));

        if ($category) {
            Mage::getSingleton('Mage_Catalog_Model_Session')->setLastVisitedCategoryId($category->getId());
            Mage::register('current_category', $category);
            try {
                Mage::dispatchEvent(
                    'catalog_controller_category_init_after',
                    array(
                        'category'          => $category,
                        'controller_action' => $this
                    )
                );
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                return false;
            }
        }

        return $category;
    }

    /**
     * Category view action
     */
    public function viewAction()
    {
        if ($category = $this->_initCatagory()) {
            /** @var $serviceManager Mage_Catalog_ServiceManager */
            $serviceManager = Mage::getSingleton('Mage_Catalog_ServiceManager');
            /** @var $layoutService Mage_Core_Service_Type_LayoutUtility */
            $layoutService = $serviceManager->getService('layout');
            $layout = $layoutService->getLayout($this->_currentArea);

            $layout->getUpdate()->addHandle('default');

            $design = Mage::getSingleton('Mage_Catalog_Model_Design');
            $settings = $design->getDesignSettings($category);

            // apply custom design
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            $update = $layout->getUpdate();
            if ($category->getIsAnchor()) {
                $type = $category->hasChildren() ? 'layered' : 'layered_without_children';
            } else {
                $type = $category->hasChildren() ? 'default' : 'default_without_children';
            }
            $this->addPageLayoutHandles(
                array('type' => $type, 'id' => $category->getId())
            );

            $layoutService->loadLayoutUpdates($layout);

            // apply custom layout update once layout is loaded
            if ($layoutUpdates = $settings->getLayoutUpdates()) {
                if (is_array($layoutUpdates)) {
                    foreach ($layoutUpdates as $layoutUpdate) {
                        $update->addUpdate($layoutUpdate);
                    }
                }
            }

            $layoutService->generateLayoutXml($layout);

            $layoutService->generateLayoutBlocks($layout);

            // apply custom layout (page) template once the blocks are generated
            if ($settings->getPageLayout()) {
                $layout->helper('Mage_Page_Helper_Layout')->applyTemplate($settings->getPageLayout());
            }

            if ($root = $this->getLayout()->getBlock('root')) {
                $root->addBodyClass('categorypath-' . $category->getUrlPath())
                    ->addBodyClass('category-' . $category->getUrlKey());
            }

            $this->_initLayoutMessages('Mage_Catalog_Model_Session');
            $this->_initLayoutMessages('Mage_Checkout_Model_Session');

            $output = $layoutService->renderLayout($layout);
            $this->getResponse()->setBody($output);
        } elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }
}
