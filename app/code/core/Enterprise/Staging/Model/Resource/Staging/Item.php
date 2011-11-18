<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Staging item resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Staging_Item extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_staging_item', 'staging_item_id');
    }

    /**
     * Before save processing
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Staging_Model_Resource_Staging_Item
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $staging = $object->getStaging();
        if ($staging instanceof Enterprise_Staging_Model_Staging) {
            if ($staging->getId()) {
                $object->setStagingId($staging->getId());
            }
        }
        return parent::_beforeSave($object);
    }
}
