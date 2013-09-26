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
 * Cms page edit form revisions tab
 */
class Magento_VersionsCms_Block_Adminhtml_Cms_Page_Revision_Edit_Info extends Magento_Adminhtml_Block_Widget_Container
{
    /**
     * Currently loaded page model
     *
     * @var Magento_Cms_Model_Page
     */
    protected $_page;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Magento_User_Model_UserFactory
     */
    protected $_userFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_User_Model_UserFactory $userFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_User_Model_UserFactory $userFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_locale = $locale;
        $this->_authSession = $authSession;
        $this->_userFactory = $userFactory;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_page = $this->_coreRegistry->registry('cms_page');
    }

    /**
     * Prepare version identifier. It should be
     * label or id if first one not assigned.
     * Also can be N/A.
     *
     * @return string
     */
    public function getVersion()
    {
        if ($this->_page->getLabel()) {
            $version = $this->_page->getLabel();
        } else {
            $version = $this->_page->getVersionId();
        }
        return $version ? $version : __('N/A');
    }

    /**
     * Prepare version number.
     *
     * @return string
     */
    public function getVersionNumber()
    {
        return $this->_page->getVersionNumber() ? $this->_page->getVersionNumber() : __('N/A');
    }

    /**
     * Prepare version label.
     *
     * @return string
     */
    public function getVersionLabel()
    {
        return $this->_page->getLabel() ? $this->_page->getLabel() : __('N/A');
    }

    /**
     * Prepare revision identifier.
     *
     * @return string
     */
    public function getRevisionId()
    {
        return $this->_page->getRevisionId() ? $this->_page->getRevisionId() : __('N/A');
    }

    /**
     * Prepare revision number.
     *
     * @return string
     */
    public function getRevisionNumber()
    {
        return $this->_page->getRevisionNumber();
    }

    /**
     * Prepare author identifier.
     *
     * @return string
     */
    public function getAuthor()
    {
        $userId = $this->_page->getUserId();
        if ($this->_authSession->getUser()->getId() == $userId) {
            return $this->_authSession->getUser()->getUsername();
        }

        $user = $this->_userFactory->create()->load($userId);

        if ($user->getId()) {
            return $user->getUsername();
        }
        return __('N/A');
    }

    /**
     * Prepare time of creation for current revision.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        $format = $this->_locale->getDateTimeFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
        $data = $this->_page->getRevisionCreatedAt();
        try {
            $data = $this->_locale->date($data, Magento_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
        } catch (Exception $e) {
            $data = __('N/A');
        }
        return  $data;
    }
}
