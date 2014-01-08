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
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Edit\Tab;

class Versions
    extends \Magento\Backend\Block\Widget\Grid\Extended
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Array of admin users in system
     *
     * @var array
     */
    protected $_usersHash = null;

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
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @var \Magento\VersionsCms\Model\Resource\Page\Version\CollectionFactory
     */
    protected $_versionCollFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\VersionsCms\Helper\Data $cmsData
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\VersionsCms\Model\Resource\Page\Version\CollectionFactory $versionCollFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\VersionsCms\Helper\Data $cmsData,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\VersionsCms\Model\Resource\Page\Version\CollectionFactory $versionCollFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_cmsData = $cmsData;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_cmsConfig = $cmsConfig;
        $this->_versionCollFactory = $versionCollFactory;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setId('versions');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepares collection of versions
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Edit\Tab\Versions
     */
    protected function _prepareCollection()
    {
        $userId = $this->_backendAuthSession->getUser()->getId();

        /* var $collection \Magento\VersionsCms\Model\Resource\Page\Revision\Collection */
        $collection = $this->_versionCollFactory->create()
            ->addPageFilter($this->getPage())
            ->addVisibilityFilter($userId, $this->_cmsConfig->getAllowedAccessLevel())
            ->addUserColumn()
            ->addUserNameColumn();

        if (!$this->getParam($this->getVarNameSort())) {
            $collection->addNumberSort();
        }

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
     * Prepare versions grid columns
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Edit\Tab\Versions
     */
    protected function _prepareColumns()
    {
        $this->addColumn('label', array(
            'header' => __('Version Label'),
            'index' => 'label',
            'type' => 'options',
            'options' => $this->getCollection()
                                ->getAsArray('label', 'label')
        ));

        $this->addColumn('owner', array(
            'header' => __('Owner'),
            'index' => 'username',
            'type' => 'options',
            'options' => $this->getCollection()->getUsersArray(false),
            'width' => 250
        ));

        $this->addColumn('access_level', array(
            'header' => __('Access Level'),
            'index' => 'access_level',
            'type' => 'options',
            'width' => 100,
            'options' => $this->_cmsData->getVersionAccessLevels()
        ));

        $this->addColumn('revisions', array(
            'header' => __('Quantity'),
            'index' => 'revisions_count',
            'type' => 'number'
        ));

        $this->addColumn('created_at', array(
            'width'     => 150,
            'header'    => __('Created'),
            'index'     => 'created_at',
            'type'      => 'datetime',
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
        return $this->getUrl('adminhtml/*/versions', array('_current' => true));
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
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Versions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Versions');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare massactions for this grid.
     * For now it is only ability to remove versions
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Edit\Tab\Versions
     */
    protected function _prepareMassaction()
    {
        if ($this->_cmsConfig->canCurrentUserDeleteVersion()) {
            $this->setMassactionIdField('version_id');
            $this->getMassactionBlock()->setFormFieldName('version');

            $this->getMassactionBlock()->addItem('delete', array(
                'label'    => __('Delete'),
                'url'      => $this->getUrl('adminhtml/*/massDeleteVersions', array('_current' => true)),
                'confirm'  => __('Are you sure?'),
                'selected' => true,
            ));
        }
        return $this;
    }

    /**
     * Grid row event edit url
     *
     * @param object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/cms_page_version/edit', array(
            'page_id' => $row->getPageId(),
            'version_id' => $row->getVersionId()
        ));
    }
}
