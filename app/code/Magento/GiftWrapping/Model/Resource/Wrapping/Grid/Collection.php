<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
