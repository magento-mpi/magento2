<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml transaction details grid
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Transactions\Detail;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Data\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Data\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Data\CollectionFactory $collectionFactory,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    /**
     * Initialize default sorting and html ID
     */
    protected function _construct()
    {
        $this->setId('transactionDetailsGrid');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * Prepare collection for grid
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        foreach ($this->getTransactionAdditionalInfo() as $key => $value) {
            $data = new \Magento\Object(array('key' => $key, 'value' => $value));
            $collection->addItem($data);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('key', array(
            'header'    => __('Key'),
            'index'     => 'key',
            'sortable'  => false,
            'type'      => 'text',
            'header_css_class'  => 'col-key',
            'column_css_class'  => 'col-key'
        ));

        $this->addColumn('value', array(
            'header'    => __('Value'),
            'index'     => 'value',
            'sortable'  => false,
            'type'      => 'text',
            'escape'    => true,
            'header_css_class'  => 'col-value',
            'column_css_class'  => 'col-value'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve Transaction addtitional info
     *
     * @return array
     */
    public function getTransactionAdditionalInfo()
    {
        $info = $this->_coreRegistry->registry('current_transaction')->getAdditionalInformation(
            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS
        );
        return (is_array($info)) ? $info : array();
    }
}
