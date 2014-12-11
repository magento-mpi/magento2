<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision;

use Magento\Framework\App\RequestInterface;

class RevisionProvider
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\VersionsCms\Model\Page\RevisionFactory
     */
    protected $revisionFactory;

    /**
     * @param \Magento\Backend\Model\Auth\Session $auth
     * @param \Magento\VersionsCms\Model\Config $config
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\Page\RevisionFactory $revisionFactory
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $auth,
        \Magento\VersionsCms\Model\Config $config,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\Page\RevisionFactory $revisionFactory
    ) {
        $this->_backendAuthSession = $auth;
        $this->_cmsConfig = $config;
        $this->_coreRegistry = $registry;
        $this->revisionFactory = $revisionFactory;
    }

    /**
     * Prepare and place revision model into registry
     * with loaded data if id parameter present
     *
     * @param string $revisionId
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\VersionsCms\Model\Page\Revision
     */
    public function get($revisionId, RequestInterface $request)
    {
        $revision = $this->revisionFactory->create();
        $userId = $this->_backendAuthSession->getUser()->getId();
        $accessLevel = $this->_cmsConfig->getAllowedAccessLevel();

        if ($revisionId) {
            $revision->loadWithRestrictions($accessLevel, $userId, $revisionId);
        } else {
            // loading empty revision
            $versionId = (int)$request->getParam('version_id');
            $pageId = (int)$request->getParam('page_id');

            // loading empty revision but with general data from page and version
            $revision->loadByVersionPageWithRestrictions($versionId, $pageId, $accessLevel, $userId);
            $revision->setUserId($userId);
        }

        //setting in registry as cms_page to make work CE blocks
        $this->_coreRegistry->register('cms_page', $revision);
        return $revision;
    }
}
