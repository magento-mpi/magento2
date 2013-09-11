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
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\VersionsCms\Model\Page;

class Revision extends \Magento\Core\Model\AbstractModel
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
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_config;

    protected $_cacheTag = 'CMS_REVISION';

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('\Magento\VersionsCms\Model\Resource\Page\Revision');
        $this->_config = \Mage::getSingleton('Magento\VersionsCms\Model\Config');
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
            $tags[] = \Magento\Cms\Model\Page::CACHE_TAG.'_'.$this->getPageId();
        }
        return $tags;
    }

    /**
     * Preparing data before save
     *
     * @return \Magento\VersionsCms\Model\Page\Revision
     */
    protected function _beforeSave()
    {
        /*
         * Reseting revision id this revision should be saved as new.
         * Bc data was changed or original version id not equals to new version id.
         */
        if ($this->_revisionedDataWasModified() || $this->getVersionId() != $this->getOrigData('version_id')) {
            $this->unsetData($this->getIdFieldName());
            $this->setCreatedAt(\Mage::getSingleton('Magento\Core\Model\Date')->gmtDate());

            $incrementNumber = \Mage::getModel('Magento\VersionsCms\Model\Increment')
                ->getNewIncrementId(\Magento\VersionsCms\Model\Increment::TYPE_PAGE,
                        $this->getVersionId(), \Magento\VersionsCms\Model\Increment::LEVEL_REVISION);

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
     * @return \Magento\VersionsCms\Model\Page\Revision
     */
    public function publish()
    {
        $this->_getResource()->beginTransaction();
        try {
            $data = $this->_prepareDataForPublish($this);
            $object = \Mage::getModel('Magento\VersionsCms\Model\Page\Revision')->setData($data);
            $this->_getResource()->publish($object, $this->getPageId());
            $this->_getResource()->commit();
        } catch (\Exception $e){
            $this->_getResource()->rollBack();
            throw $e;
        }
        $this->cleanModelCache();
        return $this;
    }

    /**
     * Checking some moments before we can actually delete revision
     *
     * @return \Magento\VersionsCms\Model\Page\Revision
     */
    protected function _beforeDelete()
    {
        $resource = $this->_getResource();
        /* @var $resource \Magento\VersionsCms\Model\Resource\Page\Revision */
        if ($resource->isRevisionPublished($this)) {
            \Mage::throwException(
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
     * @return \Magento\VersionsCms\Model\Page\Revision
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
     * @return \Magento\VersionsCms\Model\Page\Revision
     */
    public function loadByVersionPageWithRestrictions($versionId, $pageId, $accessLevel, $userId)
    {
        $this->_getResource()->loadByVersionPageWithRestrictions($this, $versionId, $pageId, $accessLevel, $userId);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }
}
