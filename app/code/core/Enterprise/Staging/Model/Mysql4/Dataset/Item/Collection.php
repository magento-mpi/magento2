<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Staging_Model_Mysql4_Dataset_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('enterprise_staging/dataset_item');
    }

    /**
     * Add dataset filter into collection
     *
<<<<<<< .mine
     * @param   mixed   $datasetId (if object must be implemented getId() method)
     * @return  Enterprise_Staging_Model_Mysql4_Dataset_Item_Collection
=======
     * @param int $datasetId
     * @return object Enterprise_Staging_Model_Mysql4_Dataset_Item_Collection
>>>>>>> .theirs
     */
    public function addDatasetFilter($datasetId)
    {
        if (is_object($datasetId)) {
            $datasetId = $datasetId->getId();
        }
        $this->addFieldToFilter('dataset_id', (int) $datasetId);

        return $this;
    }

    /**
<<<<<<< .mine
     * Add is_backend attribute filter into collection
=======
     * Set filter ignore is_backend items
>>>>>>> .theirs
     *
<<<<<<< .mine
     * @param   boolean   $flag
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging
=======
     * @param   mixed   $ignoreBackendFlag
     * @return  object  Enterprise_Staging_Model_Mysql4_Dataset_Item_Collection
>>>>>>> .theirs
     */
    public function addBackendFilter($ignoreBackendFlag = null)
    {
        if (!is_null($ignoreBackendFlag)) {
            $this->addFieldToFilter('is_backend', array('nin' => (int) $ignoreBackendFlag));
        }

        return $this;
    }

    public function toOptionArray()
    {
        return parent::_toOptionArray('dataset_item_id', 'name');
    }

    public function toOptionHash()
    {
        return parent::_toOptionHash('dataset_item_id', 'name');
    }
}
