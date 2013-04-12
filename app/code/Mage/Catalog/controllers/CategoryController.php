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
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            return false;
        }

//        $category = Mage::getModel('Mage_Catalog_Model_Category')
//            ->setStoreId(Mage::app()->getStore()->getId())
//            ->load($categoryId);

//        if (!Mage::helper('Mage_Catalog_Helper_Category')->canShow($category)) {
//            return false;
//        }

// BEGIN: MDS-87 prototype

//EXAMPLE 0 (doesn't work for now)
// we may try to follow this way, but this will require us to use classReflection to read methods' signatures
//        $storeId = Mage::app()->getStore()->getId();
//        $category = Mage::getSingleton('Mage_Core_Service_Manager')
//          ->call('Mage_Catalog_Service_Category', 'item', $categoryId, $storeId);
//EXAMPLE 0

//EXAMPLE 1
//        $category = Mage::getSingleton('Mage_Core_Service_Manager')->call('Mage_Catalog_Service_Category', 'item',
//            array(
//                'entity_id' => $categoryId,
//                'store_id'  => Mage::app()->getStore()->getId()
//            )
//        );
//EXAMPLE 1

          $service = Mage::getSingleton('Mage_Core_Service_Manager')->getService('Mage_Catalog_Service_Category');

//EXAMPLE 2
//        $category = $service->item(array(
//                'entity_id' => $categoryId,
//                'store_id'  => Mage::app()->getStore()->getId()
//            )
//        );
//EXAMPLE 2

//EXAMPLE 3
        $category = $service->call('item',
            array(
                'entity_id' => $categoryId,
                'store_id'  => Mage::app()->getStore()->getId()
            )
        );
//EXAMPLE 3

        // this is definitely not the standard service method, but a sort of helper
        // TODO how-to call such methods and where they should be declared?
        // should it be defined as a part of context to apply or to do not apply this validation, so we can move this check into service method `item`?
        // or maybe a service helper class will be useful: $service->getHelper('view')->canShow($category);
        // so will be able to use specific helpers, such as `view` or `session` and so on
        if (!$service->canShow($category)) {
            return false;
        }

// END: MDS-87 prototype

        Mage::getSingleton('Mage_Catalog_Model_Session')->setLastVisitedCategoryId($category->getId());
        Mage::register('current_category', $category);
        try {
            Mage::dispatchEvent(
                'catalog_controller_category_init_after',
                array(
                    'category' => $category,
                    'controller_action' => $this
                )
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $category;
    }

    /**
     * Category view action
     */
    public function viewAction()
    {
        if ($category = $this->_initCatagory()) {
            $design = Mage::getSingleton('Mage_Catalog_Model_Design');
            $settings = $design->getDesignSettings($category);

            // apply custom design
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            Mage::getSingleton('Mage_Catalog_Model_Session')->setLastViewedCategoryId($category->getId());

            $update = $this->getLayout()->getUpdate();
            if ($category->getIsAnchor()) {
                $type = $category->hasChildren() ? 'layered' : 'layered_without_children';
            } else {
                $type = $category->hasChildren() ? 'default' : 'default_without_children';
            }
            $this->addPageLayoutHandles(
                array('type' => $type, 'id' => $category->getId())
            );
            $this->loadLayoutUpdates();

            // apply custom layout update once layout is loaded
            if ($layoutUpdates = $settings->getLayoutUpdates()) {
                if (is_array($layoutUpdates)) {
                    foreach($layoutUpdates as $layoutUpdate) {
                        $update->addUpdate($layoutUpdate);
                    }
                }
            }

            $this->generateLayoutXml()->generateLayoutBlocks();
            // apply custom layout (page) template once the blocks are generated
            if ($settings->getPageLayout()) {
                $this->getLayout()->helper('Mage_Page_Helper_Layout')->applyTemplate($settings->getPageLayout());
            }

            if ($root = $this->getLayout()->getBlock('root')) {
                $root->addBodyClass('categorypath-' . $category->getUrlPath())
                    ->addBodyClass('category-' . $category->getUrlKey());
            }

            $this->_initLayoutMessages('Mage_Catalog_Model_Session');
            $this->_initLayoutMessages('Mage_Checkout_Model_Session');
            $this->renderLayout();
        }
        elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }
}
