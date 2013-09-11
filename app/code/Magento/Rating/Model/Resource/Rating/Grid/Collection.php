<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating grid collection
 *
 * @category    Magento
 * @package     Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rating\Model\Resource\Rating\Grid;

class Collection extends \Magento\Rating\Model\Resource\Rating\Collection
{
    /**
     * Add entity filter
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection|\Magento\Rating\Model\Resource\Rating\Grid\Collection
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addEntityFilter(\Mage::registry('entityId'));
        return $this;
    }
}
