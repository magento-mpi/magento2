<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Index_Block_Adminhtml_Process_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Process model
     *
     * @var Mage_Index_Model_Process
     */
    protected $_processModel;

    /**
     * Mass-action block
     *
     * @var string
     */
    protected $_massactionBlockName = 'Mage_Index_Block_Adminhtml_Process_Grid_Massaction';

    /**
     * Event repository
     *
     * @var Mage_Index_Model_EventRepository
     */
    protected $_eventRepository;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Mage_Index_Model_EventRepository $eventRepository
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Mage_Index_Model_EventRepository $eventRepository,
        array $data = array()
    ) {
        parent::__construct($context, $storeManager, $urlModel, $data);
        $this->_eventRepository = $eventRepository;
    }

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_processModel = Mage::getSingleton('Mage_Index_Model_Process');
        $this->setId('indexer_processes_grid');
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    /**
     * Prepare grid collection
     *
     * @return Mage_Index_Block_Adminhtml_Process_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Index_Model_Resource_Process_Collection');
        $this->setCollection($collection);
        parent::_prepareCollection();

        return $this;
    }

    /**
     * Add name and description to collection elements
     *
     * @return Mage_Index_Block_Adminhtml_Process_Grid
     */
    protected function _afterLoadCollection()
    {
        /** @var $item Mage_Index_Model_Process */
        foreach ($this->_collection as $key => $item) {
            if (!$item->getIndexer()->isVisible()) {
                $this->_collection->removeItemByKey($key);
                continue;
            }
            $item->setName($item->getIndexer()->getName());
            $item->setDescription($item->getIndexer()->getDescription());
            $item->setUpdateRequired($this->_eventRepository->hasUnprocessed($item) ? 1 : 0);
            if ($item->isLocked()) {
                $item->setStatus(Mage_Index_Model_Process::STATUS_RUNNING);
            }
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Mage_Index_Block_Adminhtml_Process_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('indexer_code', array(
            'header'    => Mage::helper('Mage_Index_Helper_Data')->__('Index'),
            'width'     => '180',
            'align'     => 'left',
            'index'     => 'name',
            'sortable'  => false,
        ));

        $this->addColumn('description', array(
            'header'    => Mage::helper('Mage_Index_Helper_Data')->__('Description'),
            'align'     => 'left',
            'index'     => 'description',
            'sortable'  => false,
        ));

        $this->addColumn('mode', array(
            'header'    => Mage::helper('Mage_Index_Helper_Data')->__('Mode'),
            'width'     => '150',
            'align'     => 'left',
            'index'     => 'mode',
            'type'      => 'options',
            'options'   => $this->_processModel->getModesOptions()
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('Mage_Index_Helper_Data')->__('Status'),
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => $this->_processModel->getStatusesOptions(),
            'frame_callback' => array($this, 'decorateStatus')
        ));

        $this->addColumn('update_required', array(
            'header'    => Mage::helper('Mage_Index_Helper_Data')->__('Update Required'),
            'sortable'  => false,
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'update_required',
            'type'      => 'options',
            'options'   => $this->_processModel->getUpdateRequiredOptions(),
            'frame_callback' => array($this, 'decorateUpdateRequired')
        ));

        $this->addColumn('ended_at', array(
            'header'    => Mage::helper('Mage_Index_Helper_Data')->__('Updated'),
            'type'      => 'datetime',
            'width'     => '180',
            'align'     => 'left',
            'index'     => 'ended_at',
            'frame_callback' => array($this, 'decorateDate')
        ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('Mage_Index_Helper_Data')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('Mage_Index_Helper_Data')->__('Reindex Data'),
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
     * @param Mage_Index_Model_Process $row
     * @param Magento_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getStatus()) {
            case Mage_Index_Model_Process::STATUS_PENDING :
                $class = 'grid-severity-notice';
                break;
            case Mage_Index_Model_Process::STATUS_RUNNING :
                $class = 'grid-severity-major';
                break;
            case Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX :
                $class = 'grid-severity-critical';
                break;
        }
        return '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }

    /**
     * Decorate "Update Required" column values
     *
     * @param string $value
     * @param Mage_Index_Model_Process $row
     * @param Magento_Adminhtml_Block_Widget_Grid_Column $column
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
     * @param Mage_Index_Model_Process $row
     * @param Magento_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     *
     * @return string
     */
    public function decorateDate($value, $row, $column, $isExport)
    {
        if (!$value) {
            return $this->__('Never');
        }
        return $value;
    }

    /**
     * Get row edit url
     *
     * @param Mage_Index_Model_Process $row
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
     * @return Mage_Index_Block_Adminhtml_Process_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('process_id');
        $this->getMassactionBlock()->setFormFieldName('process');

        $modeOptions = Mage::getModel('Mage_Index_Model_Process')->getModesOptions();

        $this->getMassactionBlock()->addItem('change_mode', array(
            'label'         => Mage::helper('Mage_Index_Helper_Data')->__('Change Index Mode'),
            'url'           => $this->getUrl('*/*/massChangeMode'),
            'additional'    => array(
                'mode'      => array(
                    'name'      => 'index_mode',
                    'type'      => 'select',
                    'class'     => 'required-entry',
                    'label'     => Mage::helper('Mage_Index_Helper_Data')->__('Index mode'),
                    'values'    => $modeOptions
                )
            )
        ));

        $this->getMassactionBlock()->addItem('reindex', array(
            'label'    => Mage::helper('Mage_Index_Helper_Data')->__('Reindex Data'),
            'url'      => $this->getUrl('*/*/massReindex'),
            'selected' => true,
        ));

        return $this;
    }
}
