<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model;

class CategoryRegistry
{
    /** @var  \Magento\Catalog\Model\Category[] */
    protected $categories;

    /**
     * @param $categories
     */
    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param $categoryId
     * @return \Magento\Catalog\Model\Category|null
     */
    public function get($categoryId)
    {
        return isset($this->categories[$categoryId]) ? $this->categories[$categoryId] : null;
    }

    /**
     * @return \Magento\Catalog\Model\Category[]
     */
    public function getList()
    {
        return $this->categories;
    }
}
