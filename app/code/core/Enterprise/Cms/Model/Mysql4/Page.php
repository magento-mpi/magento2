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
     * Aliast of revision table used in query
     * @var string
     */
    protected $_revisionTableAlias = 'rev_table';

    /**
     * Name of version table from config
     * @var string
     */
    protected $_versionTable;

    /**
     * Aliast of version table used in query
     * @var string
     */
    protected $_versionTableAlias = 'ver_table';

    /**
     * Configuration model
     * @var Enterprise_Cms_Model_Config
     */
    protected $_config;

    /**
     * Flag which deterimnes if native save logic will be run
     * @var bool
     */
    protected $_canRunNativeSave = false;

    /**
     * Flag which determines if native delete logic will be run
     * @var unknown_type
     */
    protected $_canRunNativeDelete = false;

    protected function _construct()
    {
        parent::_construct();
        $this->_revisionTable = $this->getTable('enterprise_cms/revision');
        $this->_versionTable = $this->getTable('enterprise_cms/version');

        $this->_config = Mage::getSingleton('enterprise_cms/config');

        $this->_canRunNativeDelete = $this->_config->isCurrentUserCanDeletePage();

        $this->_canRunNativeSave = $this->_config->isCurrentUserCanCreatePage()
                || $this->_config->isCurrentUserCanPublish();
    }

    /**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($this->_canRunNativeSave) {
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
        if ($this->_canRunNativeSave) {
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
        if ($this->_canRunNativeSave) {
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
        if ($this->_canRunNativeDelete) {
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
        if ($this->_canRunNativeDelete) {
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
        if ($this->_canRunNativeDelete) {
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
        $select = parent::_getLoadSelect($field, $value, $object);

        $_conditions = array(
            $this->getMainTable() . '.' . $this->getIdFieldName() . '='
                . $this->_revisionTableAlias . '.page_id'
        );

        /*
         * In case we have specified revision try to load it.
         * In other case try to load most new revision for this page.
         */
        if ($object->getRevisionId()) {
            $_conditions[] = $this->_revisionTableAlias . '.revision_id = ' . $object->getRevisionId();
        } else {
            $select->order('revision_id DESC')
                ->limit(1);
        }

        $select->joinLeft(array($this->_revisionTableAlias => $this->_revisionTable),
            implode(' AND ', $_conditions), array());

        /*
         * Adding access level filtering to disallow loading of closed content
         */
        $_conditions = array();

        if ($object->getUserId()) {
            $_condition[] = $this->_versionTableAlias . '.user_id = ' . $object->getUserId();
        }

        $accessLevel = $object->getAccessLevel();
        if (is_array($accessLevel) && !empty($accessLevel)) {
            $_conditions[] = $this->_versionTableAlias .
                '.access_level in ("' . implode('","', $accessLevel) . '")';
        } else if ($accessLevel) {
            $_conditions[] = $this->_versionTableAlias . '.access_level = "' . $accessLevel . '"';
        }

        if (!empty($_conditions)) {
            $_conditions = ' AND (' . implode(' OR ', $_conditions) . ')';
        } else {
            $_conditions = '';
        }

        $select->joinLeft(array($this->_versionTableAlias => $this->_versionTable),
             $this->_versionTableAlias . '.version_id = ' .
                    $this->_revisionTableAlias . '.version_id ' . $_conditions,
             '*');

        /*
         * If no revision data we need to copy it from main table
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
