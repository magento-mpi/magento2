<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter queue grid block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Newsletter\Model\Resource\Queue\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Newsletter\Model\Resource\Queue\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Newsletter\Model\Resource\Queue\CollectionFactory $collectionFactory,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('queueGrid');
        $this->setDefaultSort('start_at');
        $this->setDefaultDir('desc');

        $this->setUseAjax(true);

        $this->setEmptyText(__('No Newsletter Found'));

    }

    public function getGridUrl()
    {
        return $this->getUrl('customer/*/newsletter', array('_current' => true));
    }

    protected function _prepareCollection()
    {
        /** @var $collection \Magento\Newsletter\Model\Resource\Queue\Collection */
        $collection = $this->_collectionFactory->create()
            ->addTemplateInfo()
            ->addSubscriberFilter($this->_coreRegistry->registry('subscriber')->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('queue_id', array(
            'header'    =>  __('ID'),
            'align'     =>  'left',
            'index'     =>  'queue_id',
            'width'     =>  10
        ));

        $this->addColumn('start_at', array(
            'header'    =>  __('Start date'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'queue_start_at',
            'default'   =>  ' ---- '
        ));

        $this->addColumn('finish_at', array(
            'header'    =>  __('End Date'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'queue_finish_at',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));

        $this->addColumn('letter_sent_at', array(
            'header'    =>  __('Receive Date'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'letter_sent_at',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));

        $this->addColumn('template_subject', array(
            'header'    =>  __('Subject'),
            'align'     =>  'center',
            'index'     =>  'template_subject'
        ));

         $this->addColumn('status', array(
            'header'    =>  __('Status'),
            'align'     =>  'center',
            'filter'    =>  'Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid\Filter\Status',
            'index'     => 'queue_status',
            'renderer'  =>  'Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid\Renderer\Status'
        ));

        $this->addColumn('action', array(
            'header'    =>  __('Action'),
            'align'     =>  'center',
            'filter'    =>  false,
            'sortable'  =>  false,
            'renderer'  =>  'Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid\Renderer\Action'
        ));

        return parent::_prepareColumns();
    }
}
