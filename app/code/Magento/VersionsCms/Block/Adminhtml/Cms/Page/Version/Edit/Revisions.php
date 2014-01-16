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
 * Grid with revisions on version page
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Version\Edit;

class Revisions
    extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Cms data
     *
     * @var \Magento\VersionsCms\Helper\Data
     */
    protected $_cmsData;
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\VersionsCms\Model\Resource\Page\Revision\CollectionFactory
     */
    protected $_revisionCollFactory;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\VersionsCms\Helper\Data $cmsData
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\VersionsCms\Model\Resource\Page\Revision\CollectionFactory $revisionCollFactory
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\VersionsCms\Helper\Data $cmsData,
        \Magento\Core\Model\Registry $registry,
        \Magento\VersionsCms\Model\Resource\Page\Revision\CollectionFactory $revisionCollFactory,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_cmsData = $cmsData;
        $this->_revisionCollFactory = $revisionCollFactory;
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('revisionsGrid');
        $this->setDefaultSort('revision_number');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * Prepares collection of revisions
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Version\Edit\Revisions
     */
    protected function _prepareCollection()
    {
        /* var $collection \Magento\VersionsCms\Model\Resource\Page\Revision\Collection */
        $collection = $this->_revisionCollFactory->create()
            ->addPageFilter($this->getPage())
            ->addVersionFilter($this->getVersion())
            ->addUserColumn()
            ->addUserNameColumn();

            // Commented this bc now revision are shown in scope of version and not page.
            // So if user has permission to load this version he
            // has permission to see all its versions
            //->addVisibilityFilter($this->_objM->get('Magento\Backend\Model\Auth\Session')->getUser()->getId(),
            //$this->_cmsConfig->getAllowedAccessLevel());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Retrieve collection for grid if there is not collection call _prepareCollection
     *
     * @return \Magento\VersionsCms\Model\Resource\Page\Version\Collection
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_prepareCollection();
        }

        return $this->_collection;
    }

    /**
     * Prepare event grid columns
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Version\Edit\Revisions
     */
    protected function _prepareColumns()
    {
        $this->addColumn('revision_number', array(
            'header' => __('Revision'),
            'width' => 200,
            'type' => 'number',
            'index' => 'revision_number'
        ));

        $this->addColumn('created_at', array(
            'header' => __('Created'),
            'index' => 'created_at',
            'type' => 'datetime',
            'filter_time' => true,
            'width' => 250
        ));

        $this->addColumn('author', array(
            'header' => __('Author'),
            'index' => 'user',
            'type' => 'options',
            'options' => $this->getCollection()->getUsersArray()
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare url for reload grid through ajax
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/*/revisions', array('_current' => true));
    }

    /**
     * Grid row event edit url
     *
     * @param object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/cms_page_revision/edit', array(
            'page_id' => $row->getPageId(),
            'revision_id' => $row->getRevisionId()
        ));
    }

    /**
     * Returns cms page object from registry
     *
     * @return \Magento\Cms\Model\Page
     */
    public function getPage()
    {
        return $this->_coreRegistry->registry('cms_page');
    }

    /**
     * Returns cms page version object from registry
     *
     * @return \Magento\VersionsCms\Model\Page\Version
     */
    public function getVersion()
    {
        return $this->_coreRegistry->registry('cms_page_version');
    }

    /**
     * Prepare massactions for this grid.
     * For now it is only ability to remove revisions
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Version\Edit\Revisions
     */
    protected function _prepareMassaction()
    {
        if ($this->_cmsConfig->canCurrentUserDeleteRevision()) {
            $this->setMassactionIdField('revision_id');
            $this->getMassactionBlock()->setFormFieldName('revision');

            $this->getMassactionBlock()->addItem('delete', array(
                 'label'=> __('Delete'),
                 'url'  => $this->getUrl('adminhtml/*/massDeleteRevisions', array('_current' => true)),
                 'confirm' => __('Are you sure?')
            ));
        }
        return $this;
    }
}
