<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit;

/**
 * Cms page edit form revisions tab
 */
class Info extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Currently loaded page model
     *
     * @var \Magento\Cms\Model\Page
     */
    protected $_page;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\User\Model\UserFactory $userFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_authSession = $authSession;
        $this->_userFactory = $userFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
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
        $format = $this->_localeDate->getDateTimeFormat(
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM
        );
        $data = $this->_page->getRevisionCreatedAt();
        try {
            $data = $this->_localeDate->date(
                $data,
                \Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT
            )->toString(
                $format
            );
        } catch (\Exception $e) {
            $data = __('N/A');
        }
        return $data;
    }
}
