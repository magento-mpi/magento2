<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_Staging_Model_Mysql4_Dataset_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('enterprise_staging/dataset_item');
    }

    /**
     * Set dataset filter into collection select
     *
     * @param int $datasetId
     * @return object Enterprise_Staging_Model_Mysql4_Staging
     */
    public function addDatasetFilter($datasetId)
    {
        $this->addFieldToFilter('dataset_id', (int) $datasetId);

        return $this;
    }

    /**
     * Set is_backend attribute filter into collection
     *
     * @param mixed $flag if object must be forced to int
     * @return object Enterprise_Staging_Model_Mysql4_Staging
     */
    public function addBackendFilter($flag = 0)
    {
        $this->addFieldToFilter('is_backend', (int) $flag);

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