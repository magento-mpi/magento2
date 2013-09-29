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
 * Cms page revision collection
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\VersionsCms\Model\Resource\Page\Collection;

abstract class AbstractCollection
    extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Array of admin users in loaded collection
     *
     * @var array
     */
    protected $_usersHash  = null;

    /**
     * Initialization
     *
     */
    protected function _construct()
    {
        $this->_map['fields']['user_id'] = 'main_table.user_id';
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

    /**
     * Add filtering by page id.
     * Parameter $page can be int or cms page object.
     *
     * @param mixed $page
     * @return \Magento\VersionsCms\Model\Resource\Page\Collection\AbstractCollection
     */
    public function addPageFilter($page)
    {
        if ($page instanceof \Magento\Cms\Model\Page) {
            $page = $page->getId();
        }

        if (is_array($page)) {
            $page = array('in' => $page);
        }

        $this->addFieldTofilter('page_id', $page);

        return $this;
    }

    /**
     * Adds filter by version access level specified by owner.
     *
     * @param mixed $userId
     * @param mixed $accessLevel
     * @return \Magento\VersionsCms\Model\Resource\Page\Collection\AbstractCollection
     */
    public function addVisibilityFilter($userId, $accessLevel = \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC)
    {
        $_condition = array();

        if (is_array($userId)) {
            $_condition[] = $this->_getConditionSql(
                $this->_getMappedField('user_id'), array('in' => $userId));
        } else if ($userId){
            $_condition[] = $this->_getConditionSql(
                $this->_getMappedField('user_id'), $userId);
        }

        if (is_array($accessLevel)) {
            $_condition[] = $this->_getConditionSql(
                $this->_getMappedField('access_level'), array('in' => $accessLevel));
        } else {
            $_condition[] = $this->_getConditionSql(
                $this->_getMappedField('access_level'), $accessLevel);
        }

        $this->getSelect()->where(implode(' OR ', $_condition));

        return $this;
    }

    /**
     * Mapping user_id to user column with additional value for non-existent users
     *
     * @return \Magento\VersionsCms\Model\Resource\Page\Collection\AbstractCollection
     */
    public function addUserColumn()
    {
        $userField = $this->getConnection()->getIfNullSql('main_table.user_id', '-1');
        $this->getSelect()->columns(array('user' => $userField));

        $this->_map['fields']['user'] = $userField;

        return $this;
    }

    /**
     * Join username from system user table
     *
     * @return \Magento\VersionsCms\Model\Resource\Page\Collection\AbstractCollection
     */
    public function addUserNameColumn()
    {
        if (!$this->getFlag('user_name_column_joined')) {
            $userField = $this->getConnection()->getIfNullSql('ut.username', '-1');
            $this->getSelect()->joinLeft(
                array('ut' => $this->getTable('admin_user')),
                'ut.user_id = main_table.user_id',
                array('username' => $userField));

            $this->setFlag('user_name_column_joined', true);
        }

        return $this;
    }

    /**
     * Retrieve array of admin users in collection
     *
     * @param bool $idAsKey default true if false then name will be used as key and value
     * @return array
     */
    public function getUsersArray($idAsKey = true)
    {
        if (!$this->_usersHash) {
            $this->_usersHash = array();
            foreach ($this->_toOptionHash('user_id', 'username') as $userId => $username) {
                if ($userId) {
                    if ($idAsKey) {
                        $this->_usersHash[$userId]   = $username;
                    } else {
                        $this->_usersHash[$username] = $username;
                    }
                } else {
                    $this->_usersHash['-1'] = __('[No Owner]');
                }
            }

            ksort($this->_usersHash);
        }
        return $this->_usersHash;
    }

    /**
     * Add filtering by user id.
     *
     * @param int|null $userId
     * @return \Magento\VersionsCms\Model\Resource\Page\Collection\AbstractCollection
     */
    public function addUserIdFilter($userId = null)
    {
        if ($userId === null) {
            $condition = array('null' => true);
        } else {
            $condition = (int)$userId;
        }

        $this->addFieldTofilter('user_id', $condition);

        return $this;
    }
}
