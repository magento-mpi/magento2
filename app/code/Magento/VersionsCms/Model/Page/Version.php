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
 * @method Magento_VersionsCms_Model_Resource_Page_Version _getResource()
 * @method Magento_VersionsCms_Model_Resource_Page_Version getResource()
 * @method string getLabel()
 * @method Magento_VersionsCms_Model_Page_Version setLabel(string $value)
 * @method string getAccessLevel()
 * @method Magento_VersionsCms_Model_Page_Version setAccessLevel(string $value)
 * @method int getPageId()
 * @method Magento_VersionsCms_Model_Page_Version setPageId(int $value)
 * @method int getUserId()
 * @method Magento_VersionsCms_Model_Page_Version setUserId(int $value)
 * @method int getRevisionsCount()
 * @method Magento_VersionsCms_Model_Page_Version setRevisionsCount(int $value)
 * @method int getVersionNumber()
 * @method Magento_VersionsCms_Model_Page_Version setVersionNumber(int $value)
 * @method string getCreatedAt()
 * @method Magento_VersionsCms_Model_Page_Version setCreatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_VersionsCms_Model_Page_Version extends Magento_Core_Model_Abstract
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
        $this->_init('Magento_VersionsCms_Model_Resource_Page_Version');
    }

    /**
     * Preparing data before save
     *
     * @return Magento_VersionsCms_Model_Page_Version
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $incrementNumber = Mage::getModel('Magento_VersionsCms_Model_Increment')
                ->getNewIncrementId(Magento_VersionsCms_Model_Increment::TYPE_PAGE,
                        $this->getPageId(), Magento_VersionsCms_Model_Increment::LEVEL_VERSION);

            $this->setVersionNumber($incrementNumber);
            $this->setCreatedAt(Mage::getSingleton('Magento_Core_Model_Date')->gmtDate());
        }

        if (!$this->getLabel()) {
            Mage::throwException(__('Please enter a version label.'));
        }

        // We cannot allow changing access level for some versions
        if ($this->getAccessLevel() != $this->getOrigData('access_level')) {
            if ($this->getOrigData('access_level') == Magento_VersionsCms_Model_Page_Version::ACCESS_LEVEL_PUBLIC) {
                $resource = $this->_getResource();
                /* @var $resource Magento_VersionsCms_Model_Resource_Page_Version */

                if ($resource->isVersionLastPublic($this)) {
                    Mage::throwException(
                        __('Cannot change version access level because it is the last public version for its page.')
                    );
                }

//                if ($resource->isVersionHasPublishedRevision($this)) {
//                    Mage::throwException(
//                        __('Cannot change version access level because its revision is published.')
//                    );
//                }
            }
        }

        return parent::_beforeSave();
    }

    /**
     * Processing some data after version saved
     *
     * @return Magento_VersionsCms_Model_Page_Version
     */
    protected function _afterSave()
    {
        // If this was a new version we should create initial revision for it
        // from specified revision or from latest for parent version
        if ($this->getOrigData($this->getIdFieldName()) != $this->getId()) {
            $revision = Mage::getModel('Magento_VersionsCms_Model_Page_Revision');

            // setting data for load
            $userId = $this->getUserId();
            $accessLevel = Mage::getSingleton('Magento_VersionsCms_Model_Config')->getAllowedAccessLevel();

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
     * @return Magento_VersionsCms_Model_Page_Version
     */
    protected function _beforeDelete()
    {
        $resource = $this->_getResource();
        /* @var $resource Magento_VersionsCms_Model_Resource_Page_Version */
        if ($this->isPublic()) {
            if ($resource->isVersionLastPublic($this)) {
                Mage::throwException(
                    __('Version "%1" cannot be removed because it is the last public page version.', $this->getLabel())
                );
            }
        }

        if ($resource->isVersionHasPublishedRevision($this)) {
            Mage::throwException(
                __('Version "%1" cannot be removed because its revision is published.', $this->getLabel())
            );
        }

        return parent::_beforeDelete();
    }

    /**
     * Removing unneeded data from increment table after version was removed.
     *
     * @param $observer
     * @return Magento_VersionsCms_Model_Observer
     */
    protected function _afterDelete()
    {
        Mage::getResourceSingleton('Magento_VersionsCms_Model_Resource_Increment')
            ->cleanIncrementRecord(Magento_VersionsCms_Model_Increment::TYPE_PAGE,
                $this->getId(),
                Magento_VersionsCms_Model_Increment::LEVEL_REVISION);

        return parent::_afterDelete();
    }

    /**
     * Check if this version public or not.
     *
     * @return bool
     */
    public function isPublic()
    {
        return $this->getAccessLevel() == Magento_VersionsCms_Model_Page_Version::ACCESS_LEVEL_PUBLIC;
    }

    /**
     * Loading version with extra access level checking.
     *
     * @param array|string $accessLevel
     * @param int $userId
     * @param int|string $value
     * @param string|null $field
     * @return Magento_VersionsCms_Model_Page_Version
     */
    public function loadWithRestrictions($accessLevel, $userId, $value, $field = null)
    {
        $this->_getResource()->loadWithRestrictions($this, $accessLevel, $userId, $value, $field = null);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }
}
