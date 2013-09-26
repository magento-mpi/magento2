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
 * Cms page revision model
 *
 * @method Magento_VersionsCms_Model_Resource_Page_Revision _getResource()
 * @method Magento_VersionsCms_Model_Resource_Page_Revision getResource()
 * @method int getVersionId()
 * @method Magento_VersionsCms_Model_Page_Revision setVersionId(int $value)
 * @method int getPageId()
 * @method Magento_VersionsCms_Model_Page_Revision setPageId(int $value)
 * @method string getRootTemplate()
 * @method Magento_VersionsCms_Model_Page_Revision setRootTemplate(string $value)
 * @method string getMetaKeywords()
 * @method Magento_VersionsCms_Model_Page_Revision setMetaKeywords(string $value)
 * @method string getMetaDescription()
 * @method Magento_VersionsCms_Model_Page_Revision setMetaDescription(string $value)
 * @method string getContentHeading()
 * @method Magento_VersionsCms_Model_Page_Revision setContentHeading(string $value)
 * @method string getContent()
 * @method Magento_VersionsCms_Model_Page_Revision setContent(string $value)
 * @method string getCreatedAt()
 * @method Magento_VersionsCms_Model_Page_Revision setCreatedAt(string $value)
 * @method string getLayoutUpdateXml()
 * @method Magento_VersionsCms_Model_Page_Revision setLayoutUpdateXml(string $value)
 * @method string getCustomTheme()
 * @method Magento_VersionsCms_Model_Page_Revision setCustomTheme(string $value)
 * @method string getCustomRootTemplate()
 * @method Magento_VersionsCms_Model_Page_Revision setCustomRootTemplate(string $value)
 * @method string getCustomLayoutUpdateXml()
 * @method Magento_VersionsCms_Model_Page_Revision setCustomLayoutUpdateXml(string $value)
 * @method string getCustomThemeFrom()
 * @method Magento_VersionsCms_Model_Page_Revision setCustomThemeFrom(string $value)
 * @method string getCustomThemeTo()
 * @method Magento_VersionsCms_Model_Page_Revision setCustomThemeTo(string $value)
 * @method int getUserId()
 * @method Magento_VersionsCms_Model_Page_Revision setUserId(int $value)
 * @method int getRevisionNumber()
 * @method Magento_VersionsCms_Model_Page_Revision setRevisionNumber(int $value)
 */
class Magento_VersionsCms_Model_Page_Revision extends Magento_Core_Model_Abstract
{
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
     * @var Magento_VersionsCms_Model_Config
     */
    protected $_config;

    /**
     * @var string
     */
    protected $_cacheTag = 'CMS_REVISION';

    /**
     * @var Magento_Core_Model_Date
     */
    protected $_coreDate;

    /**
     * @var Magento_VersionsCms_Model_IncrementFactory
     */
    protected $_cmsIncrementFactory;

    /**
     * @var Magento_VersionsCms_Model_Page_RevisionFactory
     */
    protected $_pageRevisionFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_VersionsCms_Model_Config $cmsConfig
     * @param Magento_Core_Model_Date $coreDate
     * @param Magento_VersionsCms_Model_IncrementFactory $cmsIncrementFactory
     * @param Magento_VersionsCms_Model_Page_RevisionFactory $pageRevisionFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_VersionsCms_Model_Config $cmsConfig,
        Magento_Core_Model_Date $coreDate,
        Magento_VersionsCms_Model_IncrementFactory $cmsIncrementFactory,
        Magento_VersionsCms_Model_Page_RevisionFactory $pageRevisionFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
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
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_VersionsCms_Model_Resource_Page_Revision');
    }

    /**
     * Get cahce tags associated with object id
     *
     * @return array
     */
    public function getCacheIdTags()
    {
        $tags = parent::getCacheIdTags();
        if ($tags && $this->getPageId()) {
            $tags[] = Magento_Cms_Model_Page::CACHE_TAG . '_' . $this->getPageId();
        }
        return $tags;
    }

    /**
     * Preparing data before save
     *
     * @return Magento_VersionsCms_Model_Page_Revision
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
                Magento_VersionsCms_Model_Increment::TYPE_PAGE,
                $this->getVersionId(),
                Magento_VersionsCms_Model_Increment::LEVEL_REVISION
            );

            $this->setRevisionNumber($incrementNumber);
        }

        return parent::_beforeSave();
    }

    /**
     * Check data which is under revision control if it was modified.
     *
     * @return array
     */
    protected function _revisionedDataWasModified()
    {
        $attributes = $this->_config->getPageRevisionControledAttributes();
        foreach ($attributes as $attr) {
            $value = $this->getData($attr);
            if ($this->getOrigData($attr) !== $value) {
                if ($this->getOrigData($attr) === NULL && $value === '' || $value === NULL) {
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
     * @return Magento_VersionsCms_Model_Page_Revision
     * @throws Exception
     */
    public function publish()
    {
        $this->_getResource()->beginTransaction();
        try {
            $data = $this->_prepareDataForPublish($this);
            $object = $this->_pageRevisionFactory->create()->setData($data);
            $this->_getResource()->publish($object, $this->getPageId());
            $this->_getResource()->commit();
        } catch (Exception $e){
            $this->_getResource()->rollBack();
            throw $e;
        }
        $this->cleanModelCache();
        return $this;
    }

    /**
     * Checking some moments before we can actually delete revision
     *
     * @return Magento_VersionsCms_Model_Page_Revision
     * @throws Magento_Core_Exception
     */
    protected function _beforeDelete()
    {
        $resource = $this->_getResource();
        /* @var $resource Magento_VersionsCms_Model_Resource_Page_Revision */
        if ($resource->isRevisionPublished($this)) {
            throw new Magento_Core_Exception(
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
     * @return Magento_VersionsCms_Model_Page_Revision
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
     * @return Magento_VersionsCms_Model_Page_Revision
     */
    public function loadByVersionPageWithRestrictions($versionId, $pageId, $accessLevel, $userId)
    {
        $this->_getResource()->loadByVersionPageWithRestrictions($this, $versionId, $pageId, $accessLevel, $userId);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }
}
