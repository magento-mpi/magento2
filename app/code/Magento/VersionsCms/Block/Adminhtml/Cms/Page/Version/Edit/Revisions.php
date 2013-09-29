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
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
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
    protected $_cmsData = null;
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\VersionsCms\Helper\Data $cmsData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\VersionsCms\Helper\Data $cmsData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_cmsData = $cmsData;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
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
        /* var $collection Magento\VersionsCms\Model\Resource\Revision\Collection */
        $collection = \Mage::getModel('Magento\VersionsCms\Model\Page\Revision')->getCollection()
            ->addPageFilter($this->getPage())
            ->addVersionFilter($this->getVersion())
            ->addUserColumn()
            ->addUserNameColumn();

            // Commented this bc now revision are shown in scope of version and not page.
            // So if user has permission to load this version he
            // has permission to see all its versions
            //->addVisibilityFilter(\Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser()->getId(),
            //    \Mage::getSingleton('Magento\VersionsCms\Model\Config')->getAllowedAccessLevel());

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
        return $this->getUrl('*/*/revisions', array('_current'=>true));
    }

    /**
     * Grid row event edit url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/cms_page_revision/edit', array('page_id' => $row->getPageId(), 'revision_id' => $row->getRevisionId()));
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
        if (\Mage::getSingleton('Magento\VersionsCms\Model\Config')->canCurrentUserDeleteRevision()) {
            $this->setMassactionIdField('revision_id');
            $this->getMassactionBlock()->setFormFieldName('revision');

            $this->getMassactionBlock()->addItem('delete', array(
                 'label'=> __('Delete'),
                 'url'  => $this->getUrl('*/*/massDeleteRevisions', array('_current' => true)),
                 'confirm' => __('Are you sure?')
            ));
        }
        return $this;
    }
}
