<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth Consumer grid block
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Oauth\Block\Adminhtml\Oauth\Consumer;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /** @var \Magento\Oauth\Model\Consumer\Factory  */
    private $_consumerFactory;

    /**
     * Allow edit status
     *
     * @var bool
     */
    protected $_editAllow = false;

    /**
     * Internal constructor. Override _construct(), not __construct().
     *
     * @param \Magento\Oauth\Model\Consumer\Factory $consumerFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     */
    public function __construct(
        \Magento\Oauth\Model\Consumer\Factory $consumerFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
        $this->_consumerFactory = $consumerFactory;
    }

    /**
     * Internal constructor: override this in subclasses
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('consumerGrid');
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('entity_id')->setDefaultDir(\Magento\DB\Select::SQL_DESC);
        $this->_editAllow = $this->_authorization->isAllowed('Magento_Oauth::consumer_edit');
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_consumerFactory->create()->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Oauth\Block\Adminhtml\Oauth\Consumer\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => __('ID'),
            'index'  => 'entity_id',
            'align'  => 'right',
            'width'  => '50px'
        ));

        $this->addColumn('name', array(
            'header' => __('Add-On Name'),
            'index'  => 'name',
            'escape' => true
        ));

        $this->addColumn('http_post_url', array(
            'header' => __('Http Post URL'),
            'index'  => 'http_post_url',
        ));

        $this->addColumn('created_at', array(
            'header' => __('Created'),
            'index'  => 'created_at'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Get row URL
     *
     * @param \Magento\Oauth\Model\Consumer $row
     * @return string|null
     */
    public function getRowUrl($row)
    {
        if ($this->_editAllow) {
            return $this->getUrl('*/*/edit', array('id' => $row->getId()));
        }
        return null;
    }
}
