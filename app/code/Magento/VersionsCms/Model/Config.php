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
 * Enterprise cms page config model
 */
class Magento_VersionsCms_Model_Config
{
    const XML_PATH_CONTENT_VERSIONING = 'cms/content/versioning';

    /**
     * @var array
     */
    protected $_revisionControlledAttributes = array(
        'page' => array(
            'root_template',
            'meta_keywords',
            'meta_description',
            'content_heading',
            'content',
            'layout_update_xml',
            'custom_theme',
            'custom_root_template',
            'custom_layout_update_xml',
            'custom_theme_from',
            'custom_theme_to'
        ));

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_backendAuthSession;

    /**
     * @param Magento_AuthorizationInterface $authorization
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Backend_Model_Auth_Session $backendAuthSession
     */
    public function __construct(
        Magento_AuthorizationInterface $authorization,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Backend_Model_Auth_Session $backendAuthSession
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_authorization = $authorization;
        $this->_backendAuthSession = $backendAuthSession;
    }

    /**
     * Retrieves attributes for passed cms
     * type excluded from revision control.
     *
     * @param string $type
     * @return array
     */
    protected function _getRevisionControledAttributes($type)
    {
        if (isset($this->_revisionControlledAttributes[$type])) {
            return $this->_revisionControlledAttributes[$type];
        }
        return array();
    }

    /**
     * Retrieves cms page's attributes which are under revision control.
     *
     * @return array
     */
    public function getPageRevisionControledAttributes()
    {
        return $this->_getRevisionControledAttributes('page');
    }

    /**
     * Returns array of access levels which can be viewed by current user.
     *
     * @return array
     */
    public function getAllowedAccessLevel()
    {
        if ($this->canCurrentUserPublishRevision()) {
            return array(
                Magento_VersionsCms_Model_Page_Version::ACCESS_LEVEL_PROTECTED,
                Magento_VersionsCms_Model_Page_Version::ACCESS_LEVEL_PUBLIC
            );
        } else {
            return array(Magento_VersionsCms_Model_Page_Version::ACCESS_LEVEL_PUBLIC);
        }
    }

    /**
     * Returns status of current user publish permission.
     *
     * @return bool
     */
    public function canCurrentUserPublishRevision()
    {
        return $this->_authorization->isAllowed('Magento_VersionsCms::publish_revision');
    }

    /**
     * Return status of current user delete page permission.
     *
     * @return bool
     */
    public function canCurrentUserDeletePage()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page_delete');
    }

    /**
     * Return status of current user create new page permission.
     *
     * @return bool
     */
    public function canCurrentUserSavePage()
    {
        return $this->_authorization->isAllowed('Magento_Cms::save');
    }

    /**
     * Return status of current user permission to save revision.
     *
     * @return bool
     */
    public function canCurrentUserSaveRevision()
    {
        return $this->_authorization->isAllowed('Magento_VersionsCms::save_revision');
    }

    /**
     * Return status of current user permission to delete revision.
     *
     * @return bool
     */
    public function canCurrentUserDeleteRevision()
    {
        return $this->_authorization->isAllowed('Magento_VersionsCms::delete_revision');
    }

    /**
     * Return status of current user permission to save version.
     *
     * @return bool
     */
    public function canCurrentUserSaveVersion()
    {
        return $this->canCurrentUserSaveRevision();
    }

    /**
     * Return status of current user permission to delete version.
     *
     * @return bool
     */
    public function canCurrentUserDeleteVersion()
    {
        return $this->canCurrentUserDeleteRevision();
    }

    /**
     * Compare current user with passed owner of version or author of revision.
     *
     * @param $userId
     * @return bool
     */
    public function isCurrentUserOwner($userId)
    {
        return $this->_backendAuthSession->getUser()->getId() == $userId;
    }

    /**
     * Get default value for versioning from configuration.
     *
     * @return bool
     */
    public function getDefaultVersioningStatus()
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_CONTENT_VERSIONING);
    }
}
