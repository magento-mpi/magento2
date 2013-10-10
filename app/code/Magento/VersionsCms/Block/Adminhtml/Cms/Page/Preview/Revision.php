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
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview;

class Revision extends \Magento\Adminhtml\Block\Template
{
    /**
     * @var \Magento\VersionsCms\Model\Resource\Page\Revision\CollectionFactory
     */
    protected $_revisionCollFactory;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\VersionsCms\Model\Resource\Page\Revision\CollectionFactory $revisionCollFactory
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\VersionsCms\Model\Resource\Page\Revision\CollectionFactory $revisionCollFactory,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
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
        /* var $collection \Magento\VersionsCms\Model\Resource\Page\Revision\Collection */
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
