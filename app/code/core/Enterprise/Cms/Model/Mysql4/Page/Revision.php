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

class Enterprise_Cms_Model_Mysql4_Page_Revision extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Name of page table from config
     * @var string
     */
    protected $_pageTable;

    /**
     * Name of version table from config
     * @var string
     */
    protected $_versionTable;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/page_revision', 'revision_id');
        $this->_pageTable = $this->getTable('cms/page');
        $this->_versionTable = $this->getTable('enterprise_cms/page_version');
    }

    /**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Cms_Model_Mysql4_Page_Revision
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getCopiedFromOriginal()) {
            $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            foreach (array('custom_theme_from', 'custom_theme_to') as $dataKey) {
                $date = $object->getData($dataKey);
                if (!$date) {
                    $object->setData($dataKey, new Zend_Db_Expr('NULL'));
                }
            }
        }
        return parent::_beforeSave($object);
    }

    /**
     * Process data after save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Cms_Model_Mysql4_Page_Revision
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_aggregateVersionData((int)$object->getVersionId());

        return parent::_afterSave($object);
    }

    /**
     * Process data after delete
     * Validate if this revision can be removed
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Cms_Model_Mysql4_Page_Revision
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        $this->_aggregateVersionData((int)$object->getVersionId());

        return parent::_afterDelete($object);
    }

    /**
     * Checking some moments before we can actually delete revision
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Cms_Model_Mysql4_Page_Revision
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_beforeDelete($object);

        /*
         * Checking if this revision was not published
         */
        $select = $this->_getReadAdapter()->select();
        $select->from($this->_pageTable, 'published_revision_id')
            ->where('page_id = ?', $object->getPageId());

        $result = $this->_getReadAdapter()->fetchOne($select);

        if ($result == $object->getId()) {
            Mage::throwException(
                Mage::helper('enterprise_cms')->__('Revision #%s could not be removed because it is selected for publishing.', $object->getRevisionNumber())
            );
        }

        $select = $this->_getReadAdapter()->select();
        $select->from($this->getMainTable(), 'count(*)')
            ->where('revision_id <> ?', $object->getId())
            ->where('version_id = ? ', $object->getVersionId());

        $result = $this->_getReadAdapter()->fetchOne($select);

        if (!$result) {
            Mage::throwException(
                Mage::helper('enterprise_cms')->__('Revision #%s could not be removed because it is last available in its version.', $object->getRevisionNumber())
            );
        }

        return $this;
    }

    /**
     * Aggregate data for version
     *
     * @param int $versionId
     * @return unknown_type
     */
    protected function _aggregateVersionData($versionId)
    {
        $versionTable = $this->getTable('enterprise_cms/page_version');

        $select = 'UPDATE `' . $versionTable . '` SET `revisions_count` =
            (SELECT count(*) from `' . $this->getMainTable() . '` where `version_id` = ' . (int)$versionId . ')
            where `version_id` = ' . (int)$versionId;

        $this->_getWriteAdapter()->query($select);

        return $this;
    }

    /**
     * Retrieve select object for load object data.
     * Joining revision controled data from extra tables.
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        /*
         * Adding version data and access level filtering
         * to disallow loading of closed content
         */
        $conditions = array('ver_table.user_id = ' . (int)$object->getUserId());

        $accessLevel = $object->getAccessLevel();
        if (is_array($accessLevel) && !empty($accessLevel)) {
            $conditions[] = 'ver_table.access_level in ("' . implode('","', $accessLevel) . '")';
        } else if ($accessLevel) {
            $conditions[] = 'ver_table.access_level = "' . $accessLevel . '"';
        } else {
            $conditions[] = 'ver_table.access_level = ""';
        }

        $conditions = ' AND (' . implode(' OR ', $conditions) . ')';

        $select->join(array('ver_table' => $this->_versionTable),
            'ver_table.version_id = ' . $this->getMainTable() . '.version_id' . $conditions,
            array('version_id', 'version_number', 'label', 'access_level', 'version_user_id' => 'user_id'));

        /*
         * Adding page data.
         */
        $select->join(array('page_table' => $this->_pageTable),
            $this->getMainTable() . '.page_id=page_table.page_id', array('title'));

        return $select;
    }

    /**
     * Publishing passed revision object to page
     *
     * @param array $object
     * @param int $targetId
     * @return Enterprise_Cms_Model_Mysql4_Page_Revision
     */
    public function publish(array $data, $targetId)
    {
        $data = $this->_prepareDataForPublish($data);
        $condition = $this->_getWriteAdapter()->quoteInto('page_id = ?', $targetId);
        $this->_getWriteAdapter()->update($this->_pageTable, $data, $condition);

        return $this;
    }

    /**
     * Prepare data for publish
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  array
     */
    protected function _prepareDataForPublish(array $data)
    {
        $_preparedData = array();
        $fields = $this->_getWriteAdapter()->describeTable($this->_pageTable);
        foreach (array_keys($fields) as $field) {
            if (isset($data[$field])) {
                $fieldValue = $data[$field];
                if ($fieldValue instanceof Zend_Db_Expr) {
                    $_preparedData[$field] = $fieldValue;
                }
                else {
                    if (null !== $fieldValue) {
                        $_preparedData[$field] = $this->_prepareValueForSave($fieldValue, $fields[$field]['DATA_TYPE']);
                    }
                    elseif (!empty($fields[$field]['NULLABLE'])) {
                        $_preparedData[$field] = null;
                    }
                }
            }
        }
        return $_preparedData;
    }
}
