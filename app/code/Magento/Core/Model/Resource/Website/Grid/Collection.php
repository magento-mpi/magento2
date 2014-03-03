<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\Website\Grid;

/**
 * Grid collection
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Website\Collection
{
    /**
     * Join website and store names
     *
     * @return $this
     */
    protected function  _initSelect()
    {
        parent::_initSelect();
        $this->joinGroupAndStore();
        return $this;
    }
}
