<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Page;

/**
 * Cms page revision model
 *
 * @method \Magento\VersionsCms\Model\Resource\Page\Revision _getResource()
 * @method \Magento\VersionsCms\Model\Resource\Page\Revision getResource()
 * @method int getVersionId()
 * @method \Magento\VersionsCms\Model\Page\Revision setVersionId(int $value)
 * @method int getPageId()
 * @method \Magento\VersionsCms\Model\Page\Revision setPageId(int $value)
 * @method string getRootTemplate()
 * @method \Magento\VersionsCms\Model\Page\Revision setRootTemplate(string $value)
 * @method string getMetaKeywords()
 * @method \Magento\VersionsCms\Model\Page\Revision setMetaKeywords(string $value)
 * @method string getMetaDescription()
 * @method \Magento\VersionsCms\Model\Page\Revision setMetaDescription(string $value)
 * @method string getContentHeading()
 * @method \Magento\VersionsCms\Model\Page\Revision setContentHeading(string $value)
 * @method string getContent()
 * @method \Magento\VersionsCms\Model\Page\Revision setContent(string $value)
 * @method string getCreatedAt()
 * @method \Magento\VersionsCms\Model\Page\Revision setCreatedAt(string $value)
 * @method string getLayoutUpdateXml()
 * @method \Magento\VersionsCms\Model\Page\Revision setLayoutUpdateXml(string $value)
 * @method string getCustomTheme()
 * @method \Magento\VersionsCms\Model\Page\Revision setCustomTheme(string $value)
 * @method string getCustomRootTemplate()
 * @method \Magento\VersionsCms\Model\Page\Revision setCustomRootTemplate(string $value)
 * @method string getCustomLayoutUpdateXml()
 * @method \Magento\VersionsCms\Model\Page\Revision setCustomLayoutUpdateXml(string $value)
 * @method string getCustomThemeFrom()
 * @method \Magento\VersionsCms\Model\Page\Revision setCustomThemeFrom(string $value)
 * @method string getCustomThemeTo()
 * @method \Magento\VersionsCms\Model\Page\Revision setCustomThemeTo(string $value)
 * @method int getUserId()
 * @method \Magento\VersionsCms\Model\Page\Revision setUserId(int $value)
 * @method int getRevisionNumber()
 * @method \Magento\VersionsCms\Model\Page\Revision setRevisionNumber(int $value)
 */
class Revision extends \Magento\Model\AbstractModel implements \Magento\Object\IdentityInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'CMS_REVISION';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_versionscms_revision';

    /**
     * Parameter name in event.
     * In observe method you can use $observer->getEvent()->getObject() in this case.
     *
     * @var string
     */
    protected $_eventObject = 'revision';

    /**
     * Configuration model
     *
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_config;

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var \Magento\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    /**
     * @var \Magento\VersionsCms\Model\IncrementFactory
     */
    protected $_cmsIncrementFactory;

    /**
     * @var \Magento\VersionsCms\Model\Page\RevisionFactory
     */
    protected $_pageRevisionFactory;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\VersionsCms\Model\IncrementFactory $cmsIncrementFactory
     * @param \Magento\VersionsCms\Model\Page\RevisionFactory $pageRevisionFactory
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\Stdlib\DateTime\DateTime $coreDate,
        \Magento\VersionsCms\Model\IncrementFactory $cmsIncrementFactory,
        \Magento\VersionsCms\Model\Page\RevisionFactory $pageRevisionFactory,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_config = $cmsConfig;
        $this->_coreDate = $coreDate;
        $this->_cmsIncrementFactory = $cmsIncrementFactory;
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
        $this->_init('Magento\VersionsCms\Model\Resource\Page\Revision');
    }

    /**
     * Preparing data before save
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        /**
         * Resetting revision id this revision should be saved as new.
         * Bc data was changed or original version id not equals to new version id.
         */
        if ($this->_revisionedDataWasModified() || $this->getVersionId() != $this->getOrigData('version_id')) {
            $this->unsetData($this->getIdFieldName());
            $this->setCreatedAt($this->_coreDate->gmtDate());

            $incrementNumber = $this->_cmsIncrementFactory->create()->getNewIncrementId(
                \Magento\VersionsCms\Model\Increment::TYPE_PAGE,
                $this->getVersionId(),
                \Magento\VersionsCms\Model\Increment::LEVEL_REVISION
            );

            $this->setRevisionNumber($incrementNumber);
        }

        return parent::_beforeSave();
    }

    /**
     * Check data which is under revision control if it was modified.
     *
     * @return bool
     */
    protected function _revisionedDataWasModified()
    {
        $attributes = $this->_config->getPageRevisionControledAttributes();
        foreach ($attributes as $attr) {
            $value = $this->getData($attr);
            if ($this->getOrigData($attr) !== $value) {
                if ($this->getOrigData($attr) === null && $value === '' || $value === null) {
                    continue;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Prepare data which must be published
     *
     * @return array
     */
    protected function _prepareDataForPublish()
    {
        $data = array();
        $attributes = $this->_config->getPageRevisionControledAttributes();
        foreach ($this->getData() as $key => $value) {
            if (in_array($key, $attributes)) {
                $this->unsetData($key);
                $data[$key] = $value;
            }
        }

        $data['published_revision_id'] = $this->getId();

        return $data;
    }

    /**
     * Publishing current revision
     *
     * @return $this
     * @throws \Exception
     */
    public function publish()
    {
        $this->_getResource()->beginTransaction();
        try {
            $data = $this->_prepareDataForPublish($this);
            $object = $this->_pageRevisionFactory->create()->setData($data);
            $this->_getResource()->publish($object, $this->getPageId());
            $this->_getResource()->commit();
        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        $this->cleanModelCache();
        return $this;
    }

    /**
     * Checking some moments before we can actually delete revision
     *
     * @return void
     * @throws \Magento\Model\Exception
     */
    protected function _beforeDelete()
    {
        $resource = $this->_getResource();
        /* @var $resource \Magento\VersionsCms\Model\Resource\Page\Revision */
        if ($resource->isRevisionPublished($this)) {
            throw new \Magento\Model\Exception(
                __('Revision #%1 could not be removed because it is published.', $this->getRevisionNumber())
            );
        }
    }

    /**
     * Loading revision with extra access level checking.
     *
     * @param array|string $accessLevel
     * @param int $userId
     * @param int|string $value
     * @param string|null $field
     * @return $this
     */
    public function loadWithRestrictions($accessLevel, $userId, $value, $field = null)
    {
        $this->_getResource()->loadWithRestrictions($this, $accessLevel, $userId, $value, $field);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Loading revision with empty data which is under
     * control and with other data from version and page.
     * Also apply extra access level checking.
     *
     * @param int $versionId
     * @param int $pageId
     * @param array|string $accessLevel
     * @param int $userId
     * @return $this
     */
    public function loadByVersionPageWithRestrictions($versionId, $pageId, $accessLevel, $userId)
    {
        $this->_getResource()->loadByVersionPageWithRestrictions($this, $versionId, $pageId, $accessLevel, $userId);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return array(self::CACHE_TAG . '_' . $this->getId());
    }
}
