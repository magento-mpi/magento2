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
 * Revision selector
 */
class Magento_VersionsCms_Block_Adminhtml_Cms_Page_Preview_Revision extends Magento_Adminhtml_Block_Template
{
    /**
     * @var Magento_VersionsCms_Model_Resource_Page_Revision_CollectionFactory
     */
    protected $_revisionCollFactory;

    /**
     * @var Magento_VersionsCms_Model_Config
     */
    protected $_cmsConfig;

    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_backendAuthSession;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_VersionsCms_Model_Resource_Page_Revision_CollectionFactory $revisionCollFactory
     * @param Magento_VersionsCms_Model_Config $cmsConfig
     * @param Magento_Backend_Model_Auth_Session $backendAuthSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_VersionsCms_Model_Resource_Page_Revision_CollectionFactory $revisionCollFactory,
        Magento_VersionsCms_Model_Config $cmsConfig,
        Magento_Backend_Model_Auth_Session $backendAuthSession,
        array $data = array()
    ) {
        $this->_revisionCollFactory = $revisionCollFactory;
        $this->_cmsConfig = $cmsConfig;
        $this->_backendAuthSession = $backendAuthSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve id of currently selected revision
     *
     * @return int
     */
    public function getRevisionId()
    {
        if (!$this->hasRevisionId()) {
            $this->setData('revision_id', (int)$this->getRequest()->getPost('preview_selected_revision'));
        }
        return $this->getData('revision_id');
    }

    /**
     * Prepare array with revisions sorted by versions
     *
     * @return array
     */
    public function getRevisions()
    {
        /* var $collection Magento_VersionsCms_Model_Resource_Page_Revision_Collection */
        $collection = $this->_revisionCollFactory->create()
            ->addPageFilter($this->getRequest()->getParam('page_id'))
            ->joinVersions()
            ->addNumberSort()
            ->addVisibilityFilter(
                $this->_backendAuthSession->getUser()->getId(),
                $this->_cmsConfig->getAllowedAccessLevel()
        );

        $revisions = array();

        foreach ($collection->getItems() as $item) {
            if (isset($revisions[$item->getVersionId()])) {
                $revisions[$item->getVersionId()]['revisions'][] = $item;
            } else {
                $revisions[$item->getVersionId()] = array(
                    'revisions' => array($item),
                    'label' => ($item->getLabel() ? $item->getLabel() : __('N/A'))
                );
            }
        }
        krsort($revisions);
        reset($revisions);
        return $revisions;
    }
}
