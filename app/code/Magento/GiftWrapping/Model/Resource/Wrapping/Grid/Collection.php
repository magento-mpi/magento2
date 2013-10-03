<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Wrapping Collection
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 */
namespace Magento\GiftWrapping\Model\Resource\Wrapping\Grid;

class Collection
    extends \Magento\GiftWrapping\Model\Resource\Wrapping\Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addStoreAttributesToResult();
        $this->addWebsitesToResult();
        return $this;
    }
}
