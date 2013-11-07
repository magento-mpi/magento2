<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Helper;

/**
 * Class Categories Helper
 *
 * @package Magento\TestFramework\Helper
 */
class Categories
{
    /**
     * Object manager
     *
     * @var \Magento\Core\Model\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Get categories
     *
     * @var array
     */
    protected $_categories = array();

    /**
     * Categories count
     *
     * @var int
     */
    protected $_categoriesNumber = 0;

    /**
     * Constructor
     */
    public function __construct()
    {

        $rootCategoryId = $this->getObjectManager()->create('\Magento\Core\Model\StoreManager')->getDefaultStoreView()
            ->getRootCategoryId();

        /** @var $category \Magento\Catalog\Model\Category */
        $category = $this->getObjectManager()->get('Magento\Catalog\Model\Category');
        $category->load($rootCategoryId);

        /** @var $categoryResource \Magento\Catalog\Model\Resource\Category */
        $categoryResource = $category->getResource();
        $categories = $categoryResource->getAllChildren($category);
        $this->_categoriesNumber = count($categories);

        /**
         * Preapre categories paths for import
         *
         * @see \Magento\ImportExport\Model\Import\Entity\Product::_initCategories()
         */
        foreach ($categories as $key => $categoryId) {
            $category->load($categoryId);
            $structure = explode('/', $category->getPath());
            $pathSize  = count($structure);
            if ($pathSize > 1) {
                $path = array();
                for ($i = 1; $i < $pathSize; $i++) {
                    $path[] = $category->load($structure[$i])->getName();
                }
                array_shift($path);
                $categories[$key] = implode('/', $path);
            } else {
                $categories[$key] = $category->getName();
            }
        }

        /** Removing store root categories */
        $this->_categories = array_filter($categories);
    }

    /**
     * Get object manager
     *
     * @return \Magento\Core\Model\ObjectManager|null
     */
    protected function getObjectManager()
    {
        if (!$this->_objectManager) {
            $this->_objectManager = new \Magento\Core\Model\ObjectManager(
                new \Magento\Core\Model\Config\Primary(BP, $_SERVER)
            );
            /** @var $app \Magento\Core\Model\App */
            $this->_objectManager->get('Magento\Core\Model\App');
        }

        return $this->_objectManager;
    }

    /**
     * Get for import number by increment
     *
     * @param $index
     *
     * @return mixed
     */
    public function getCategoryForImport($index)
    {
        return $this->_categories[$index % $this->_categoriesNumber];
    }
}
