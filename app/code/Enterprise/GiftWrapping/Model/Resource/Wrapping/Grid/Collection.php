<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Wrapping Collection
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 */
class Enterprise_GiftWrapping_Model_Resource_Wrapping_Grid_Collection
    extends Enterprise_GiftWrapping_Model_Resource_Wrapping_Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addStoreAttributesToResult();
        $this->addWebsitesToResult();
        return $this;
    }
}
