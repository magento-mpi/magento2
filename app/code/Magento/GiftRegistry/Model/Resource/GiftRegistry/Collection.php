<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Resource\GiftRegistry;

/**
 * Gift registry data grid collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\GiftRegistry\Model\Resource\Type\Collection
{
    /**
     * Add sore data for load
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addStoreData();
        return $this;
    }
}
