<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Models limiter for AdminGws module
 */
class Enterprise_Tag_Model_AdminGws_Models extends Enterprise_AdminGws_Model_Models
{

    /**
     * Validate if user has exclusive access to tag
     *
     * @param Mage_Tag_Model_Tag $model
     */
    public function tagSaveBefore($model)
    {
        $storeIds = $model->getVisibleInStoreIds();
        // Remove admin store with id 0
        $storeIds = array_filter((array)$storeIds);
        if ($model->getId() && !$this->_role->hasExclusiveStoreAccess((array)$storeIds)) {
            $this->_throwSave();
        }
    }

    /**
     * Disallow remove tag for user with limited access
     *
     * @param Mage_Tag_Model_Tag $model
     */
    public function tagDeleteBefore($model)
    {
        $this->_throwDelete();
    }

    /**
     * @throws Mage_Core_Exception
     */
    private function _throwSave()
    {
        Mage::throwException(
            Mage::helper('Enterprise_Tag_Helper_Data')->__('Not enough permissions to save this item.')
        );
    }

    /**
     * @throws Mage_Core_Exception
     */
    private function _throwDelete()
    {
        Mage::throwException(
            Mage::helper('Enterprise_Tag_Helper_Data')->__('Not enough permissions to delete this item.')
        );
    }
}
