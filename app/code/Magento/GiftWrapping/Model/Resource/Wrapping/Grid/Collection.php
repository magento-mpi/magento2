<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model\Resource\Wrapping\Grid;

/**
 * Gift Wrapping Collection
 *
 */
class Collection extends \Magento\GiftWrapping\Model\Resource\Wrapping\Collection
{
    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addStoreAttributesToResult();
        $this->addWebsitesToResult();
        return $this;
    }
}
