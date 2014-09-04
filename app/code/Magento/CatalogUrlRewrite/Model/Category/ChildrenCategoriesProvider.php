<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category;

use Magento\Catalog\Model\Category;

class ChildrenCategoriesProvider
{
    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param boolean $recursive
     * @return \Magento\Catalog\Model\Category[]
     */
    public function getChildren(Category $category, $recursive = false)
    {
        return $category->getResourceCollection()
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addIdFilter($this->getChildrenIds($category, $recursive))
            ->load();
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param boolean $recursive
     * @return int[]
     */
    protected function getChildrenIds(Category $category, $recursive = false)
    {
        $connection = $category->getResource()->getReadConnection();
        $select = $connection->select()
            ->from($category->getResource()->getEntityTable(), 'entity_id')
            ->where($connection->quoteIdentifier('path') . ' LIKE :c_path');
        $bind = ['c_path' => $category->getPath() . '/%'];
        if (!$recursive) {
            $select->where($connection->quoteIdentifier('level') . ' <= :c_level');
            $bind['c_level'] = $category->getLevel() + 1;
        }

        return $connection->fetchCol($select, $bind);
    }
}
