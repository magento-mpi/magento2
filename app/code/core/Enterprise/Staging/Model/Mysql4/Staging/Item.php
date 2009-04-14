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

class Enterprise_Staging_Model_Mysql4_Staging_Item extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_item', 'staging_item_id');
    }

    /**
     * Before save processing
     *
     * @param Varien_Object $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $staging = $object->getStaging();
        if ($staging instanceof Enterprise_Staging_Model_Staging) {
            if ($staging->getId()) {
                $object->setStagingId($staging->getId());
            }
            $object->setDatasetId($staging->getDatasetId());
        }

        $stagingWebsite = $object->getStagingWebsite();
        if ($stagingWebsite instanceof Enterprise_Staging_Model_Staging_Website) {
            if ($stagingWebsite->getId()) {
                $object->setStagingWebsiteId($stagingWebsite->getId());
            }
        }

        $stagingStore = $object->getStagingStore();
        if ($stagingStore instanceof Enterprise_Staging_Model_Staging_Store) {
            if ($stagingStore->getId()) {
                $object->setStagingStoreId($stagingStore->getId());
            }
        }

        if (!$object->getId()) {
            $value = Mage::getModel('core/date')->gmtDate();
            $object->setCreatedAt($value);

            $datasetItem = $object->getDatasetItemInstance();
            if ($datasetItem) {
                $object->addData($datasetItem->getData());
            }
        } else {
            $value = Mage::getModel('core/date')->gmtDate();
            $object->setUpdatedAt($value);
        }

    	parent::_beforeSave($object);

        return $this;
    }
}