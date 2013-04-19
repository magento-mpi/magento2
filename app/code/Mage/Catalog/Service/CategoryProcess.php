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
class Mage_Catalog_Service_CategoryProcess extends Mage_Core_Service_Type_DefaultProcess
{
    /**
     * @param mixed $request
     * @return bool
     */
    public function initCategoryToView($request)
    {
        try {
            $request = $this->prepareRequest(get_class($this), 'view', $request);

            Mage::dispatchEvent('catalog_controller_category_init_before', array('controller_action' => $request->getControllerAction()));

            Mage::dispatchEvent('catalog_category_show_before', array('context' => $request));

            $category = $this->_serviceManager->getService('Mage_Catalog_Service_CategoryEntity')->call('item', $request);

            if (!$this->canShow($category)) {
                return false;
            }

            Mage::getSingleton('Mage_Catalog_Model_Session')->setLastVisitedCategoryId($category->getId());

            Mage::register('current_category', $category);

            Mage::dispatchEvent(
                'catalog_controller_category_init_after',
                array(
                    'category'          => $category,
                    'controller_action' => $request->getControllerAction()
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

    public function view($request)
    {
        try {
            $request  = $this->prepareRequest(get_class($this), 'view', $request);

            $category = $this->initCategoryToView($request);
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

            /** @var $layoutService Mage_Core_Service_Type_LayoutUtility */
            $layoutService = $this->_serviceManager->getService('Mage_Core_Service_Type_LayoutUtility');

            /** @var $layout Mage_Core_Model_Layout */
            $layout = $layoutService->getLayout($request->getCurrentArea());

            $defaultHandle = $request->getDefaultLayoutHandle();
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

            $layoutService->load($layout, $request->getLayoutHandles());

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

            $request->getResponse()->appendBody($output);
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
