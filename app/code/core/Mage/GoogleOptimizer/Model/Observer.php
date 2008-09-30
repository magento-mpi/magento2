<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Googleoptimizer_Model_Observer
{
    /**
     * Loading product codes after load product
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function appendToProductGoogleOptimizerCodes($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $googleOptimizerModel = Mage::getModel('googleoptimizer/code_product')
            ->setEntity($product)
            ->loadScripts($product->getStoreId());

        $product->setGoogleOptimizerCodes($googleOptimizerModel);

        return $this;
    }

    /**
     * Prepare product codes for saving
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function prepareGoogleOptimizerCodes($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $request = $observer->getEvent()->getRequest();

        if ($googleOptimizer = $request->getPost('googleoptimizer')) {
            $product->setGoogleOptimizerCodes($googleOptimizer);
        }
        return $this;
    }

    /**
     * Save product codes after saving product
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function saveProductGoogleOptimizerCodes($observer)
    {
        $product = $observer->getEvent()->getProduct();

        if ($product->getGoogleOptimizerCodes()) {
            $googleOptimizer = Mage::getModel('googleoptimizer/code_product')
                ->setEntity($product)
                ->saveScripts($product->getStoreId());
        }

        return $this;
    }

    /**
     * Delete Produt Codes after deleteing product
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function deleteProductGoogleOptimizerCodes($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $googleOptimizer = Mage::getModel('googleoptimizer/code_product')
            ->setEntity($product)
            ->deleteScripts($product->getStoreId());
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function appendToPageGoogleOptimizerCodes($observer)
    {
        $cmsPage = $observer->getEvent()->getObject();
        $googleOptimizerModel = Mage::getModel('googleoptimizer/code_page')
            ->setEntity($cmsPage)
            ->loadScripts(0);
        $cmsPage->setGoogleOptimizerCodes($googleOptimizerModel);

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $observer
     * @return unknown
     */
    public function preparePageGoogleOptimizerCodes($observer)
    {
        $cmsPage = $observer->getEvent()->getPage();
        $request = $observer->getEvent()->getRequest();

        if ($googleOptimizer = $request->getPost('googleoptimizer')) {
            $cmsPage->setGoogleOptimizerCodes($googleOptimizer);
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $observer
     * @return unknown
     */
    public function savePageGoogleOptimizerCodes($observer)
    {
        $cmsPage = $observer->getEvent()->getObject();

        if ($cmsPage->getGoogleOptimizerCodes()) {
            $googleOptimizer = Mage::getModel('googleoptimizer/code_page')
                ->setEntity($cmsPage)
                ->saveScripts(0);
        }

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $observer
     * @return unknown
     */
    public function deletePageGoogleOptimizerCodes($observer)
    {
        $cmsPage = $observer->getEvent()->getObject();
        $googleOptimizer = Mage::getModel('googleoptimizer/code_page')
            ->setEntity($cmsPage)
            ->deleteScripts(0);
        return $this;
    }

    public function assignHandlers($observer)
    {
        $catalogHalper = $observer->getEvent()->getHelper();
        $helper = Mage::helper('googleoptimizer');
        $catalogHalper->addHandler('productAttribute', $helper)
            ->addHandler('categoryAttribute', $helper);
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $observer
     * @return unknown
     */
    public function appendToCategoryGoogleOptimizerCodes($observer)
    {
        $category = $observer->getEvent()->getCategory();//Zend_Debug::dump($category->getStoreId());
        $googleOptimizerModel = Mage::getModel('googleoptimizer/code_category')
            ->setEntity($category)
            ->loadScripts($category->getStoreId());
        $category->setGoogleOptimizerCodes($googleOptimizerModel);
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $observer
     * @return unknown
     */
    public function prepareCategoryGoogleOptimizerCodes($observer)
    {
        $category = $observer->getEvent()->getCategory();
        $request = $observer->getEvent()->getRequest();

        if ($googleOptimizer = $request->getPost('googleoptimizer')) {
            $category->setGoogleOptimizerCodes($googleOptimizer);
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $observer
     * @return unknown
     */
    public function saveCategoryGoogleOptimizerCodes($observer)
    {
        $category = $observer->getEvent()->getCategory();

        if ($category->getGoogleOptimizerCodes()) {
            $googleOptimizer = Mage::getModel('googleoptimizer/code_category')
                ->setEntity($category)
                ->saveScripts($category->getStoreId());
        }

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $observer
     * @return unknown
     */
    public function deleteCategoryGoogleOptimizerCodes($observer)
    {
        $category = $observer->getEvent()->getCategory();
        $googleOptimizer = Mage::getModel('googleoptimizer/code_category')
            ->setEntity($category)
            ->deleteScripts($category->getStoreId());
        return $this;
    }

}