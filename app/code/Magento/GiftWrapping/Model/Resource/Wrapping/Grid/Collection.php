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
class Magento_GiftWrapping_Model_Resource_Wrapping_Grid_Collection
    extends Magento_GiftWrapping_Model_Resource_Wrapping_Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addStoreAttributesToResult();
        $this->addWebsitesToResult();
        return $this;
    }
}
