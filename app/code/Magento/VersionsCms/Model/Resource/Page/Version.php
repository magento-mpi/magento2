<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms page version resource model
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\VersionsCms\Model\Resource\Page;

class Version extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('magento_versionscms_page_version', 'version_id');
    }

    /**
     * Checking if version id not last public for its page
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return bool
     */
    public function isVersionLastPublic(\Magento\Core\Model\AbstractModel $object)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getMainTable(), 'COUNT(*)')
            ->where(implode(' AND ', array(
                'page_id      = :page_id',
                'access_level = :access_level',
                'version_id   = :version_id'
            )));

        $bind = array(
            ':page_id'      => $object->getPageId(),
            ':access_level' => \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC,
            ':version_id'   => $object->getVersionId()
        );

        return !$this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Checking if Version does not contain published revision
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return bool
     */
    public function isVersionHasPublishedRevision(\Magento\Core\Model\AbstractModel $object)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from(array('p' => $this->getTable('cms_page')), array())
            ->where('p.page_id = ?', (int)$object->getPageId())
            ->join(
                array('r' => $this->getTable('magento_versionscms_page_revision')),
                'r.revision_id = p.published_revision_id',
                'r.version_id');

        $result = $this->_getReadAdapter()->fetchOne($select);

        return $result == $object->getVersionId();
    }

    /**
     * Add access restriction filters to allow load only by granted user.
     *
     * @param \Magento\DB\Select $select
     * @param int $accessLevel
     * @param int $userId
     * @return \Magento\DB\Select
     */
    protected function _addAccessRestrictionsToSelect($select, $accessLevel, $userId)
    {
        $conditions = array();

        $conditions[] = $this->_getReadAdapter()->quoteInto('user_id = ?', (int)$userId);

        if (!empty($accessLevel)) {
            if (!is_array($accessLevel)) {
                $accessLevel = array($accessLevel);
            }
            $conditions[] = $this->_getReadAdapter()->quoteInto('access_level IN (?)', $accessLevel);
        } else {
            $conditions[] = 'access_level IS NULL';
        }

        $select->where(implode(' OR ', $conditions));

        return $select;
    }

    /**
     * Loading data with extra access level checking.
     *
     * @param \Magento\VersionsCms\Model\Page\Version $object
     * @param array|string $accessLevel
     * @param int $userId
     * @param int|string $value
     * @param string|null $field
     * @return \Magento\VersionsCms\Model\Resource\Page\Version
     */
    public function loadWithRestrictions($object, $accessLevel, $userId, $value, $field = null)
    {
        if ($field === null) {
            $field = $this->getIdFieldName();
        }

        $read = $this->_getReadAdapter();
        if ($value) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select = $this->_addAccessRestrictionsToSelect($select, $accessLevel, $userId);
            $data   = $read->fetchRow($select);
            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);
        return $this;
    }
}
