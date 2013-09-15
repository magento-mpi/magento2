<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Index\Block\Adminhtml\Process;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * Process model
     *
     * @var \Magento\Index\Model\Process
     */
    protected $_processModel;

    /**
     * Mass-action block
     *
     * @var string
     */
    protected $_massactionBlockName = 'Magento\Index\Block\Adminhtml\Process\Grid\Massaction';

    /**
     * Event repository
     *
     * @var \Magento\Index\Model\EventRepository
     */
    protected $_eventRepository;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Index\Model\EventRepository $eventRepository
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Index\Model\EventRepository $eventRepository,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
        $this->_eventRepository = $eventRepository;
    }

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_processModel = \Mage::getSingleton('Magento\Index\Model\Process');
        $this->setId('indexer_processes_grid');
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    /**
     * Prepare grid collection
     *
     * @return \Magento\Index\Block\Adminhtml\Process\Grid
     */
    protected function _prepareCollection()
    {
        $collection = \Mage::getResourceModel('Magento\Index\Model\Resource\Process\Collection');
        $this->setCollection($collection);
        parent::_prepareCollection();

        return $this;
    }

    /**
     * Add name and description to collection elements
     *
     * @return \Magento\Index\Block\Adminhtml\Process\Grid
     */
    protected function _afterLoadCollection()
    {
        /** @var $item \Magento\Index\Model\Process */
        foreach ($this->_collection as $key => $item) {
            if (!$item->getIndexer()->isVisible()) {
                $this->_collection->removeItemByKey($key);
                continue;
            }
            $item->setName($item->getIndexer()->getName());
            $item->setDescription($item->getIndexer()->getDescription());
            $item->setUpdateRequired($this->_eventRepository->hasUnprocessed($item) ? 1 : 0);
            if ($item->isLocked()) {
                $item->setStatus(\Magento\Index\Model\Process::STATUS_RUNNING);
            }
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\Index\Block\Adminhtml\Process\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('indexer_code', array(
            'header'    => __('Index'),
            'width'     => '180',
            'align'     => 'left',
            'index'     => 'name',
            'sortable'  => false,
        ));

        $this->addColumn('description', array(
            'header'    => __('Description'),
            'align'     => 'left',
            'index'     => 'description',
            'sortable'  => false,
        ));

        $this->addColumn('mode', array(
            'header'    => __('Mode'),
            'width'     => '150',
            'align'     => 'left',
            'index'     => 'mode',
            'type'      => 'options',
            'options'   => $this->_processModel->getModesOptions()
        ));

        $this->addColumn('status', array(
            'header'    => __('Status'),
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => $this->_processModel->getStatusesOptions(),
            'frame_callback' => array($this, 'decorateStatus')
        ));

        $this->addColumn('update_required', array(
            'header'    => __('Update Required'),
            'sortable'  => false,
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'update_required',
            'type'      => 'options',
            'options'   => $this->_processModel->getUpdateRequiredOptions(),
            'frame_callback' => array($this, 'decorateUpdateRequired')
        ));

        $this->addColumn('ended_at', array(
            'header'    => __('Updated'),
            'type'      => 'datetime',
            'width'     => '180',
            'align'     => 'left',
            'index'     => 'ended_at',
            'frame_callback' => array($this, 'decorateDate')
        ));

        $this->addColumn('action',
            array(
                'header'    =>  __('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => __('Reindex Data'),
                        'url'       => array('base'=> '*/*/reindexProcess'),
                        'field'     => 'process'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
        ));

        parent::_prepareColumns();

        return $this;
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param \Magento\Index\Model\Process $row
     * @param \Magento\Adminhtml\Block\Widget\Grid\Column $column
     * @param bool $isExport
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getStatus()) {
            case \Magento\Index\Model\Process::STATUS_PENDING :
                $class = 'grid-severity-notice';
                break;
            case \Magento\Index\Model\Process::STATUS_RUNNING :
                $class = 'grid-severity-major';
                break;
            case \Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX :
                $class = 'grid-severity-critical';
                break;
        }
        return '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }

    /**
     * Decorate "Update Required" column values
     *
     * @param string $value
     * @param \Magento\Index\Model\Process $row
     * @param \Magento\Adminhtml\Block\Widget\Grid\Column $column
     * @param bool $isExport
     *
     * @return string
     */
    public function decorateUpdateRequired($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getUpdateRequired()) {
            case 0:
                $class = 'grid-severity-notice';
                break;
            case 1:
                $class = 'grid-severity-critical';
                break;
        }
        return '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }

    /**
     * Decorate last run date coumn
     *
     * @param string $value
     * @param \Magento\Index\Model\Process $row
     * @param \Magento\Adminhtml\Block\Widget\Grid\Column $column
     * @param bool $isExport
     *
     * @return string
     */
    public function decorateDate($value, $row, $column, $isExport)
    {
        if (!$value) {
            return __('Never');
        }
        return $value;
    }

    /**
     * Get row edit url
     *
     * @param \Magento\Index\Model\Process $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('process' => $row->getId()));
    }

    /**
     * Add mass-actions to grid
     *
     * @return \Magento\Index\Block\Adminhtml\Process\Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('process_id');
        $this->getMassactionBlock()->setFormFieldName('process');

        $modeOptions = \Mage::getModel('Magento\Index\Model\Process')->getModesOptions();

        $this->getMassactionBlock()->addItem('change_mode', array(
            'label'         => __('Change Index Mode'),
            'url'           => $this->getUrl('*/*/massChangeMode'),
            'additional'    => array(
                'mode'      => array(
                    'name'      => 'index_mode',
                    'type'      => 'select',
                    'class'     => 'required-entry',
                    'label'     => __('Index mode'),
                    'values'    => $modeOptions
                )
            )
        ));

        $this->getMassactionBlock()->addItem('reindex', array(
            'label'    => __('Reindex Data'),
            'url'      => $this->getUrl('*/*/massReindex'),
            'selected' => true,
        ));

        return $this;
    }
}
