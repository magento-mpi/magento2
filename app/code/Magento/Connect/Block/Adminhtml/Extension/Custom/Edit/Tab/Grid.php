<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Convert profile edit tab
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Extension collection factory
     *
     * @var \Magento\Connect\Model\Extension\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Connect\Model\Extension\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Connect\Model\Extension\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    /**
     * Initialize Grid block
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_defaultLimit = 200;
        $this->setId('extension_custom_edit_grid');
        $this->setUseAjax(true);
    }

    /**
     * Creates extension collection if it has not been created yet
     *
     * @return \Magento\Connect\Model\Extension\Collection
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_collection = $this->_collectionFactory->create();
        }
        return $this->_collection;
    }

    /**
     * Prepare Local Package Collection for Grid
     *
     * @return \Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab\Grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->getCollection());
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('folder', array(
            'header'  => __('Folder'),
            'index'   => 'folder',
            'width'   => 100,
            'type'    => 'options',
            'options' => $this->getCollection()->collectFolders()
        ));

        $this->addColumn('package', array(
            'header' => __('Package'),
            'index'  => 'package',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Self URL getter
     *
     * @return string
     */
    public function getCurrentUrl($params = array())
    {
        if (!isset($params['_current'])) {
            $params['_current'] = true;
        }
        return $this->getUrl('adminhtml/*/grid', $params);
    }

    /**
     * Row URL getter
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/*/load', array('id' => strtr(base64_encode($row->getFilenameId()), '+/=', '-_,')));
    }
}
