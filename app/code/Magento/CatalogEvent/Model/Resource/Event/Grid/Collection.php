<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event resource collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogEvent\Model\Resource\Event\Grid;

class Collection extends \Magento\CatalogEvent\Model\Resource\Event\Collection
{
    /**
     * Add category data to collection select (name, position)
     *
     * @return \Magento\CatalogEvent\Model\Resource\Event\Collection|\Magento\CatalogEvent\Model\Resource\Event\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCategoryData();
        return $this;
    }
}
