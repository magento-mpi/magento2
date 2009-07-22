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
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms page revision resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Model_Mysql4_Revision extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/revision', 'revision_id');
    }

    /**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        foreach (array('custom_theme_from', 'custom_theme_to') as $dataKey) {
            if ($date = $object->getData($dataKey)) {
                $object->setData($dataKey, Mage::app()->getLocale()->date($date, $format, null, false)
                    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                );
            } else {
                $object->setData($dataKey, new Zend_Db_Expr('NULL'));
            }
        }

        return parent::_beforeSave($object);
    }

    /**
     * Process data after save
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_aggregateVersionData((int)$object->getVersionId());

        return parent::_afterSave($object);
    }

    /**
     * Process data after delete
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        $this->_aggregateVersionData((int)$object->getVersionId());

        return parent::_afterDelete($object);
    }

    /**
     * Aggregate data for version
     *
     * @param int $versionId
     * @return unknown_type
     */
    protected function _aggregateVersionData($versionId)
    {
        $versionTable = $this->getTable('enterprise_cms/version');

        $select = 'UPDATE `' . $versionTable . '` SET `revisions_count` =
            (SELECT count(*) from `' . $this->getMainTable() . '` where `version_id` = ' . (int)$versionId . ')
            where `version_id` = ' . (int)$versionId;

        $this->_getWriteAdapter()->query($select);

        return $this;
    }
}
