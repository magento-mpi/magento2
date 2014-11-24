<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Page;

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
 */
class Version extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Access level constants
     */
    const ACCESS_LEVEL_PRIVATE = 'private';

    const ACCESS_LEVEL_PROTECTED = 'protected';

    const ACCESS_LEVEL_PUBLIC = 'public';

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
     * @var \Magento\VersionsCms\Model\IncrementFactory
     */
    protected $_cmsIncrementFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    /**
     * @var \Magento\VersionsCms\Model\Resource\Increment
     */
    protected $_cmsResourceIncrement;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @var \Magento\VersionsCms\Model\Page\RevisionFactory
     */
    protected $_pageRevisionFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\IncrementFactory $cmsIncrementFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\VersionsCms\Model\Resource\Increment $cmsResourceIncrement
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\VersionsCms\Model\Page\RevisionFactory $pageRevisionFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\IncrementFactory $cmsIncrementFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\VersionsCms\Model\Resource\Increment $cmsResourceIncrement,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\VersionsCms\Model\Page\RevisionFactory $pageRevisionFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_cmsIncrementFactory = $cmsIncrementFactory;
        $this->_coreDate = $coreDate;
        $this->_cmsResourceIncrement = $cmsResourceIncrement;
        $this->_cmsConfig = $cmsConfig;
        $this->_pageRevisionFactory = $pageRevisionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\VersionsCms\Model\Resource\Page\Version');
    }

    /**
     * Preparing data before save
     *
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    public function beforeSave()
    {
        if (!$this->getId()) {
            $incrementNumber = $this->_cmsIncrementFactory->create()->getNewIncrementId(
                \Magento\VersionsCms\Model\Increment::TYPE_PAGE,
                $this->getPageId(),
                \Magento\VersionsCms\Model\Increment::LEVEL_VERSION
            );

            $this->setVersionNumber($incrementNumber);
            $this->setCreatedAt($this->_coreDate->gmtDate());
        }

        if (!$this->getLabel()) {
            throw new \Magento\Framework\Model\Exception(__('Please enter a version label.'));
        }

        // We cannot allow changing access level for some versions
        if ($this->getAccessLevel() != $this->getOrigData('access_level')) {
            if ($this->getOrigData('access_level') == \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC) {
                $resource = $this->_getResource();
                /* @var $resource \Magento\VersionsCms\Model\Resource\Page\Version */

                if ($resource->isVersionLastPublic($this)) {
                    throw new \Magento\Framework\Model\Exception(
                        __('Cannot change version access level because it is the last public version for its page.')
                    );
                }
            }
        }

        return parent::beforeSave();
    }

    /**
     * Processing some data after version saved
     *
     * @return $this
     */
    public function afterSave()
    {
        // If this was a new version we should create initial revision for it
        // from specified revision or from latest for parent version
        if ($this->getOrigData($this->getIdFieldName()) != $this->getId()) {
            $revision = $this->_pageRevisionFactory->create();

            // setting data for load
            $userId = $this->getUserId();
            $accessLevel = $this->_cmsConfig->getAllowedAccessLevel();

            if ($this->getInitialRevisionData()) {
                $revision->setData($this->getInitialRevisionData());
            } else {
                $revision->loadWithRestrictions(
                    $accessLevel,
                    $userId,
                    $this->getOrigData($this->getIdFieldName()),
                    'version_id'
                );
            }

            $revision->setVersionId($this->getId())->setUserId($userId)->save();

            $this->setLastRevision($revision);
        }
        return parent::afterSave();
    }

    /**
     * Checking some moments before we can actually delete version
     *
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    public function beforeDelete()
    {
        $resource = $this->_getResource();
        /* @var $resource \Magento\VersionsCms\Model\Resource\Page\Version */
        if ($this->isPublic()) {
            if ($resource->isVersionLastPublic($this)) {
                throw new \Magento\Framework\Model\Exception(
                    __('Version "%1" cannot be removed because it is the last public page version.', $this->getLabel())
                );
            }
        }

        if ($resource->isVersionHasPublishedRevision($this)) {
            throw new \Magento\Framework\Model\Exception(
                __('Version "%1" cannot be removed because its revision is published.', $this->getLabel())
            );
        }

        return parent::beforeDelete();
    }

    /**
     * Removing unneeded data from increment table after version was removed.
     *
     * @return $this
     */
    public function afterDelete()
    {
        $this->_cmsResourceIncrement->cleanIncrementRecord(
            \Magento\VersionsCms\Model\Increment::TYPE_PAGE,
            $this->getId(),
            \Magento\VersionsCms\Model\Increment::LEVEL_REVISION
        );

        return parent::afterDelete();
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
     * @return $this
     */
    public function loadWithRestrictions($accessLevel, $userId, $value, $field = null)
    {
        $this->_getResource()->loadWithRestrictions($this, $accessLevel, $userId, $value, $field = null);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }
}
