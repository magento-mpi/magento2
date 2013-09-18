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
 * Cms page version model
 *
 * @method \Magento\VersionsCms\Model\Resource\Page\Version _getResource()
 * @method \Magento\VersionsCms\Model\Resource\Page\Version getResource()
 * @method string getLabel()
 * @method \Magento\VersionsCms\Model\Page\Version setLabel(string $value)
 * @method string getAccessLevel()
 * @method \Magento\VersionsCms\Model\Page\Version setAccessLevel(string $value)
 * @method int getPageId()
 * @method \Magento\VersionsCms\Model\Page\Version setPageId(int $value)
 * @method int getUserId()
 * @method \Magento\VersionsCms\Model\Page\Version setUserId(int $value)
 * @method int getRevisionsCount()
 * @method \Magento\VersionsCms\Model\Page\Version setRevisionsCount(int $value)
 * @method int getVersionNumber()
 * @method \Magento\VersionsCms\Model\Page\Version setVersionNumber(int $value)
 * @method string getCreatedAt()
 * @method \Magento\VersionsCms\Model\Page\Version setCreatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\VersionsCms\Model\Page;

class Version extends \Magento\Core\Model\AbstractModel
{
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_versionscms_version';

    /**
     * Parameter name in event.
     * In observe method you can use $observer->getEvent()->getObject() in this case.
     *
     * @var string
     */
    protected $_eventObject = 'version';

    /**
     * Access level constants
     */
    const ACCESS_LEVEL_PRIVATE = 'private';
    const ACCESS_LEVEL_PROTECTED = 'protected';
    const ACCESS_LEVEL_PUBLIC = 'public';

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\VersionsCms\Model\Resource\Page\Version');
    }

    /**
     * Preparing data before save
     *
     * @return \Magento\VersionsCms\Model\Page\Version
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $incrementNumber = \Mage::getModel('Magento\VersionsCms\Model\Increment')
                ->getNewIncrementId(\Magento\VersionsCms\Model\Increment::TYPE_PAGE,
                        $this->getPageId(), \Magento\VersionsCms\Model\Increment::LEVEL_VERSION);

            $this->setVersionNumber($incrementNumber);
            $this->setCreatedAt(\Mage::getSingleton('Magento\Core\Model\Date')->gmtDate());
        }

        if (!$this->getLabel()) {
            \Mage::throwException(__('Please enter a version label.'));
        }

        // We cannot allow changing access level for some versions
        if ($this->getAccessLevel() != $this->getOrigData('access_level')) {
            if ($this->getOrigData('access_level') == \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC) {
                $resource = $this->_getResource();
                /* @var $resource \Magento\VersionsCms\Model\Resource\Page\Version */

                if ($resource->isVersionLastPublic($this)) {
                    \Mage::throwException(
                        __('Cannot change version access level because it is the last public version for its page.')
                    );
                }
            }
        }

        return parent::_beforeSave();
    }

    /**
     * Processing some data after version saved
     *
     * @return \Magento\VersionsCms\Model\Page\Version
     */
    protected function _afterSave()
    {
        // If this was a new version we should create initial revision for it
        // from specified revision or from latest for parent version
        if ($this->getOrigData($this->getIdFieldName()) != $this->getId()) {
            $revision = \Mage::getModel('Magento\VersionsCms\Model\Page\Revision');

            // setting data for load
            $userId = $this->getUserId();
            $accessLevel = \Mage::getSingleton('Magento\VersionsCms\Model\Config')->getAllowedAccessLevel();

            if ($this->getInitialRevisionData()) {
                $revision->setData($this->getInitialRevisionData());
            } else {
                $revision->loadWithRestrictions($accessLevel, $userId, $this->getOrigData($this->getIdFieldName()), 'version_id');
            }

            $revision->setVersionId($this->getId())
                ->setUserId($userId)
                ->save();

            $this->setLastRevision($revision);
        }
        return parent::_afterSave();
    }

    /**
     * Checking some moments before we can actually delete version
     *
     * @return \Magento\VersionsCms\Model\Page\Version
     */
    protected function _beforeDelete()
    {
        $resource = $this->_getResource();
        /* @var $resource \Magento\VersionsCms\Model\Resource\Page\Version */
        if ($this->isPublic()) {
            if ($resource->isVersionLastPublic($this)) {
                \Mage::throwException(
                    __('Version "%1" cannot be removed because it is the last public page version.', $this->getLabel())
                );
            }
        }

        if ($resource->isVersionHasPublishedRevision($this)) {
            \Mage::throwException(
                __('Version "%1" cannot be removed because its revision is published.', $this->getLabel())
            );
        }

        return parent::_beforeDelete();
    }

    /**
     * Removing unneeded data from increment table after version was removed.
     *
     * @param $observer
     * @return \Magento\VersionsCms\Model\Observer
     */
    protected function _afterDelete()
    {
        \Mage::getResourceSingleton('Magento\VersionsCms\Model\Resource\Increment')
            ->cleanIncrementRecord(\Magento\VersionsCms\Model\Increment::TYPE_PAGE,
                $this->getId(),
                \Magento\VersionsCms\Model\Increment::LEVEL_REVISION);

        return parent::_afterDelete();
    }

    /**
     * Check if this version public or not.
     *
     * @return bool
     */
    public function isPublic()
    {
        return $this->getAccessLevel() == \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC;
    }

    /**
     * Loading version with extra access level checking.
     *
     * @param array|string $accessLevel
     * @param int $userId
     * @param int|string $value
     * @param string|null $field
     * @return \Magento\VersionsCms\Model\Page\Version
     */
    public function loadWithRestrictions($accessLevel, $userId, $value, $field = null)
    {
        $this->_getResource()->loadWithRestrictions($this, $accessLevel, $userId, $value, $field = null);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }
}
