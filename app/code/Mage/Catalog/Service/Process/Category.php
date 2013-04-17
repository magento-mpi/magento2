<?php
/**
 * Catalog Category Process Service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @service true
 */
class Mage_Catalog_Service_Process_Category extends Mage_Core_Service_Type_Process_Abstract
{
    /**
     * @param mixed $context
     * @return bool
     */
    public function initCategoryToView($context)
    {
        try {
            $context = $this->prepareContext(get_class($this), 'view', $context);

            Mage::dispatchEvent('catalog_controller_category_init_before', array('controller_action' => $context->getControllerAction()));

            Mage::dispatchEvent('catalog_category_show_before', array('context' => $context));

            $category = $this->_serviceManager->getService('Mage_Catalog_Service_Entity_Category')->call('item', $context);

            if (!$this->canShow($category)) {
                return false;
            }

            Mage::getSingleton('Mage_Catalog_Model_Session')->setLastVisitedCategoryId($category->getId());

            Mage::register('current_category', $category);

            Mage::dispatchEvent(
                'catalog_controller_category_init_after',
                array(
                    'category'          => $category,
                    'controller_action' => $context->getControllerAction()
                )
            );
        } catch (Mage_Core_Service_Exception $e) {
            $code = $e->getCode() ? $e->getCode() : Mage_Core_Service_Exception::HTTP_INTERNAL_ERROR;
            throw new Mage_Core_Service_Exception($e->getMessage(), $code);
        } catch (Exception $e) {
            throw new Mage_Core_Service_Exception($e->getMessage(), Mage_Core_Service_Exception::HTTP_INTERNAL_ERROR);
        }

        return $category;
    }

    public function view($context)
    {
        try {
            $context  = $this->prepareContext(get_class($this), 'view', $context);

            $category = $this->initCategoryToView($context);
            if (!$category) {
                throw new Mage_Core_Service_Exception('', Mage_Core_Service_Exception::HTTP_NOT_FOUND);
            }

            $design   = Mage::getSingleton('Mage_Catalog_Model_Design');
            $settings = $design->getDesignSettings($category);

            // apply custom design
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            Mage::getSingleton('Mage_Catalog_Model_Session')->setLastViewedCategoryId($category->getId());

            /** @var $layoutService Mage_Core_Service_Type_Utility_Layout */
            $layoutService = $this->_serviceManager->getService('Mage_Core_Service_Type_Utility_Layout');

            /** @var $layout Mage_Core_Model_Layout */
            $layout = $layoutService->getLayout($context->getCurrentArea());

            $defaultHandle = $context->getDefaultLayoutHandle();
            if ($defaultHandle) {
                if ($category->getIsAnchor()) {
                    $type = $category->hasChildren() ? 'layered' : 'layered_without_children';
                } else {
                    $type = $category->hasChildren() ? 'default' : 'default_without_children';
                }

                $layoutService->addPageLayoutHandles($layout, $defaultHandle,
                    array('type' => $type, 'id' => $category->getId())
                );
            }

            $layoutService->load($layout, $context->getLayoutHandles());

            // apply custom layout update once layout is loaded
            if ($layoutUpdates = $settings->getLayoutUpdates()) {
                if (is_array($layoutUpdates)) {
                    foreach ($layoutUpdates as $layoutUpdate) {
                        $layout->getUpdate()->addUpdate($layoutUpdate);
                    }
                }
            }

            $layoutService->generateLayoutXml($layout);

            $layoutService->generateLayoutBlocks($layout);

            // apply custom layout (page) template once the blocks are generated
            if ($settings->getPageLayout()) {
                $layout->helper('Mage_Page_Helper_Layout')->applyTemplate($settings->getPageLayout());
            }

            if ($root = $layout->getBlock('root')) {
                $root->addBodyClass('categorypath-' . $category->getUrlPath())
                    ->addBodyClass('category-' . $category->getUrlKey());
            }

            $layout->setDirectOutput(false);

            $output = $layoutService->render($layout);

            $context->getResponse()->appendBody($output);
        } catch (Mage_Core_Service_Exception $e) {
            $code = $e->getCode() ? $e->getCode() : Mage_Core_Service_Exception::HTTP_INTERNAL_ERROR;
            throw new Mage_Core_Service_Exception($e->getMessage(), $code);
        } catch (Exception $e) {
            throw new Mage_Core_Service_Exception($e->getMessage(), Mage_Core_Service_Exception::HTTP_INTERNAL_ERROR);
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Check if a category can be shown
     *
     * @param  Mage_Catalog_Model_Category $category
     * @return boolean
     */
    public function canShow($category)
    {
        if (!$category->getId()) {
            return false;
        }

        if (!$category->getIsActive()) {
            return false;
        }
        if (!$category->isInRootCategoryList()) {
            return false;
        }

        return true;
    }
}
