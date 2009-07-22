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
 * Cms Page Resource Model extended with Revison functionality
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Model_Mysql4_Page extends Mage_Cms_Model_Mysql4_Page
{
    /**
     * Name of revision table from config
     * @var string
     */
    protected $_revisionTable;

    /**
     * Name of version table from config
     * @var string
     */
    protected $_versionTable;

    /**
     * Configuration model
     * @var Enterprise_Cms_Model_Config
     */
    protected $_config;

    protected function _construct()
    {
        parent::_construct();
        $this->_revisionTable = $this->getTable('enterprise_cms/revision');
        $this->_versionTable = $this->getTable('enterprise_cms/version');

        $this->_config = Mage::getSingleton('enterprise_cms/config');
    }

    /**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->canRunNativeSave()) {
            parent::_beforeSave($object);
        }

        return $this;
    }

    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->canRunNativeSave()) {
            parent::_afterSave($object);
        }

        return $this;
    }

    /**
     * Save object object data
     *
     * @param   Mage_Core_Model_Abstract $object
     * @return  Mage_Core_Model_Mysql4_Abstract
     */
    public function save(Mage_Core_Model_Abstract $object)
    {
        if ($object->canRunNativeSave()) {
            parent::save($object);
        }

        return $this;
    }

    /**
     * Delete the object
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    public function delete(Mage_Core_Model_Abstract $object)
    {
        if ($object->canRunNativeDelete()) {
            parent::delete($object);
        }

        return $this;
    }

    /**
     * Perform actions before object delete
     *
     * @param Varien_Object $object
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        if ($object->canRunNativeDelete()) {
            parent::_beforeDelete($object);
        }

        return $this;
    }

    /**
     * Perform actions after object delete
     *
     * @param Varien_Object $object
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        if ($object->canRunNativeDelete()) {
            parent::_afterDelete($object);
        }

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
        if ($field != 'page_id') {
            Mage::throwException(Mage::helper('enterprise_cms')->__('Invalid field name for model load.'));
        }

        $select = parent::_getLoadSelect($field, $value, $object);

        /*
         * Adding version data and access level filtering
         * to disallow loading of closed content
         */
        $accessLevel = $object->getAccessLevel();
        if (is_array($accessLevel) && !empty($accessLevel)) {
            $aclCondition = 'ver_table.access_level in ("' . implode('","', $accessLevel) . '")';
        } else if ($accessLevel) {
            $aclCondition = 'ver_table.access_level = "' . $accessLevel . '"';
        }

        $conditions = array('ver_table.page_id = '
            . $this->getMainTable() . '.' . $this->getIdFieldName());

        /*
         * If we have user id we need to determine that if user
         * have his own versions of page we should show him last
         * revision from his version. If user does not have his own
         * version we should show him last available revision of other
         * users regarding to visibility of that version.
         */
        if ($object->getUserId()) {
            $subSelect = clone $select;
            $subSelect->reset();

            $subSelect->from($this->_versionTable, 'count(*)')
                ->where('user_id = ?', (int)$object->getUserId())
                ->where('page_id = ?', (int)$value);

            $conditions[] = '((ver_table.user_id <> ' . (int)$object->getUserId() .
                ' AND (' . $subSelect . ') = 0 AND ' . $aclCondition . ')
            OR ver_table.user_id = ' . (int)$object->getUserId() . ')';
        }

        if (!empty($conditions)) {
            $conditions = implode(' AND ', $conditions);
        } else {
            $conditions = '';
        }

        $select->joinLeft(array('ver_table' => $this->_versionTable),
            $conditions, array('version_id', 'label', 'access_level', 'version_user_id' => 'user_id'));

        /*
         * Adding revision data
         */
        $conditions = array('ver_table.version_id=rev_table.version_id');

        /*
         * In case we have specified revision try to load it.
         * In other case try to load most new revision for
         * this page counting on restrictions added above.
         */
        if ($object->getRevisionId()) {
            $conditions[] = 'rev_table.revision_id = ' . (int)$object->getRevisionId();
        }

        $select->order('revision_id DESC')
            ->limit(1);

        $select->joinLeft(array('rev_table' => $this->_revisionTable),
            implode(' AND ', $conditions), array('revision_id', 'revision_created_at' => 'created_at', 'user_id'));

        /*
         * In case if there is no versions and revisions available
         * we should show user initial data from published version.
         */
        $attributes = $this->_config->getPageRevisionControledAttributes();
        $_from = array();
        foreach ($attributes as $attr) {
            $_from[$attr]  ='IFNULL(rev_table.' . $attr . ', ' . $this->getMainTable() . '.' . $attr . ')';
        }

        $select->from('', $_from);

        return $select;
    }
}
