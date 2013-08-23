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
class Enterprise_Tag_Model_AdminGws_Models extends Magento_AdminGws_Model_Models
{
    /**
     * Validate if user has exclusive access to tag
     *
     * @param Magento_Tag_Model_Tag $model
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
     * @param Magento_Tag_Model_Tag $model
     */
    public function tagDeleteBefore($model)
    {
        $this->_throwDelete();
    }
}
