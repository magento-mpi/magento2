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
 * Catalog Custom Category design Model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Design extends Magento_Core_Model_Abstract
{
    const APPLY_FOR_PRODUCT     = 1;
    const APPLY_FOR_CATEGORY    = 2;

    /**
     * Apply custom design
     *
     * @param string $design
     */
    public function applyCustomDesign($design)
    {
        Mage::getDesign()->setDesignTheme($design);
        return $this;
    }

    /**
     * Get custom layout settings
     *
     * @param Magento_Catalog_Model_Category|Magento_Catalog_Model_Product $object
     * @return \Magento\Object
     */
    public function getDesignSettings($object)
    {
        if ($object instanceof Magento_Catalog_Model_Product) {
            $currentCategory = $object->getCategory();
        } else {
            $currentCategory = $object;
        }

        $category = null;
        if ($currentCategory) {
            $category = $currentCategory->getParentDesignCategory($currentCategory);
        }

        if ($object instanceof Magento_Catalog_Model_Product) {
            if ($category && $category->getCustomApplyToProducts()) {
                return $this->_mergeSettings($this->_extractSettings($category), $this->_extractSettings($object));
            } else {
                return $this->_extractSettings($object);
            }
        } else {
             return $this->_extractSettings($category);
        }
    }

    /**
     * Extract custom layout settings from category or product object
     *
     * @param Magento_Catalog_Model_Category|Magento_Catalog_Model_Product $object
     * @return \Magento\Object
     */
    protected function _extractSettings($object)
    {
        $settings = new \Magento\Object;
        if (!$object) {
            return $settings;
        }
        $date = $object->getCustomDesignDate();
        if (array_key_exists('from', $date) && array_key_exists('to', $date)
            && Mage::app()->getLocale()->isStoreDateInInterval(null, $date['from'], $date['to'])) {
                $settings->setCustomDesign($object->getCustomDesign())
                    ->setPageLayout($object->getPageLayout())
                    ->setLayoutUpdates((array)$object->getCustomLayoutUpdate());
        }
        return $settings;
    }

    /**
     * Merge custom design settings
     *
     * @param \Magento\Object $categorySettings
     * @param \Magento\Object $productSettings
     * @return \Magento\Object
     */
    protected function _mergeSettings($categorySettings, $productSettings)
    {
        if ($productSettings->getCustomDesign()) {
            $categorySettings->setCustomDesign($productSettings->getCustomDesign());
        }
        if ($productSettings->getPageLayout()) {
            $categorySettings->setPageLayout($productSettings->getPageLayout());
        }
        if ($productSettings->getLayoutUpdates()) {
            $update = array_merge($categorySettings->getLayoutUpdates(), $productSettings->getLayoutUpdates());
            $categorySettings->setLayoutUpdates($update);
        }
        return $categorySettings;
    }
}
