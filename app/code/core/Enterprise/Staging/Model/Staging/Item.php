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

/**
 * Staging item model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Staging_Item extends Mage_Core_Model_Abstract
{
    /**
     * Dataset model
     *
     * @var Enterprise_Staging_Dataset_Item
     */
    protected $_datasetItem;

    /**
     * constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_item');
    }

    /**
     * create instanse of Enterprise_Staging_Dataset_Item and put it on $this->_datasetItem
     *
     * @return Enterprise_Staging_Dataset_Item
     */
    public function getDatasetItemInstance()
    {
        if (is_null($this->_datasetItem)) {
            $this->_datasetItem = Mage::getModel('enterprise_staging/dataset_item');
            $datasetItemId = (int) $this->getData('dataset_item_id');
            if ($datasetItemId) {
                $this->_datasetItem->load($datasetItemId);
            }
        }

        return $this->_datasetItem;
    }

    /**
     * Update staging item
     *
     * @param string $attribute
     * @param unknown_type $value
     * @return Mage_Core_Model_Abstract
     */
    public function updateAttribute($attribute, $value)
    {
        return $this->getResource()->updateAttribute($this, $attribute, $value);
    }
}