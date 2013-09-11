<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleShopping Types collection
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Resource\Grid;

class Collection extends \Magento\GoogleShopping\Model\Resource\Type\Collection
{
    /**
     *  Add total count of Items for each type
     *
     * @return \Magento\GoogleShopping\Model\Resource\Grid\Collection|\Magento\GoogleShopping\Model\Resource\Type\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addItemsCount();
        return $this;
    }
}
