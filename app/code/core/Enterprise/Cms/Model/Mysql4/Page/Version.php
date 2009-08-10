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
 * Cms page version resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Model_Mysql4_Page_Version extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/page_version', 'version_id');
    }

    /**
     * Checking if version id not last public for its page
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function isVersionLastPublic(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getMainTable(), 'count(*)')
            ->where('page_id = ?', $object->getPageId())
            ->where('access_level = ?', Enterprise_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC)
            ->where('version_id <> ? ', $object->getVersionId());

        return !$this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Checking if Version does not contain published revision
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function isVersionHasPublishedRevision(Mage_Core_Model_Abstract $object)
    {

        $select = $this->_getReadAdapter()->select();
        $select->from(array('p' => $this->getTable('cms/page')), array())
            ->where('p.page_id = ?', $object->getPageId())
            ->join(array('r' => $this->getTable('enterprise_cms/page_revision')),
                'r.revision_id = p.published_revision_id', array('r.version_id'));

        $result = $this->_getReadAdapter()->fetchOne($select);

        return $result == $object->getVersionId();
    }

    /**
     * Retrieve select object for load object data and apply custom rules.
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        /*
         * Adding access level filtering to disallow loading of closed content
         */
        $conditions = array('user_id = ' . (int)$object->getUserId());

        $accessLevel = $object->getAccessLevel();
        if (is_array($accessLevel) && !empty($accessLevel)) {
            $conditions[] = 'access_level in ("' . implode('","', $accessLevel) . '")';
        } else if ($accessLevel) {
            $conditions[] = 'access_level = "' . $accessLevel . '"';
        } else {
            $conditions[] = 'access_level = ""';
        }

        $conditions = implode(' OR ', $conditions);

        $select->where($conditions);

        return $select;
    }


    /**
     * Removing orphaned versions with specified status.
     *
     * @param string|array $accessLevel
     * @return Enterprise_Cms_Model_Mysql4_Page_Version
     */
    public function cleanUpOrphanedRevisions($accessLevel)
    {
        /* @var Varien_Db_Adapter_Pdo_Mysql */
        $write = $this->_getWriteAdapter();
        $condition = array('user_id is null');

        if (!is_array($accessLevel)) {
            $accessLevel = array($accessLevel);
        }

        $condition['access_level IN (?)'] = $accessLevel;
        $write->delete($this->getMainTable(), $condition);

        return $this;
    }
}
