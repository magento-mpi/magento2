<?php
/**
 * {license_notice}
 *
 * @category    Enterise
 * @package     Enterpise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift registry data grid collection
 *
 * @category    Enterise
 * @package     Enterpise_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftRegistry_Model_Resource_GiftRegistry_Collection
    extends Enterprise_GiftRegistry_Model_Resource_Type_Collection
{
    /**
     * Add sore data for load
     *
     * @return Enterprise_GiftRegistry_Model_Resource_GiftRegistry_Collection|Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addStoreData();
        return $this;
    }
}
