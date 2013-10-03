<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid collection
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource\Website\Grid;

class Collection extends \Magento\Core\Model\Resource\Website\Collection
{
    /**
     * Join website and store names
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection|\Magento\Core\Model\Resource\Website\Grid\Collection
     */
    protected function  _initSelect()
    {
        parent::_initSelect();
        $this->joinGroupAndStore();
        return $this;
    }
}
