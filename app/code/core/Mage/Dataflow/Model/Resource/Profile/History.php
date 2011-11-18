<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Convert history resource model
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Resource_Profile_History extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('dataflow_profile_history', 'history_id');
    }

    /**
     * Sets up performed at time if needed
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Dataflow_Model_Resource_Profile_History
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getPerformedAt()) {
            $object->setPerformedAt($this->formatDate(time()));
        }
        parent::_beforeSave($object);
        return $this;
    }
}
