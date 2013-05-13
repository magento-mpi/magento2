<?php
/**
 * Adminhtml AdminNotification inbox grid
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_AdminNotification_Block_Grid extends Mage_Backend_Block_Widget_Grid_Extended
{
    /**
     * @var Mage_AdminNotification_Model_InboxFactory
     */
    protected $_inboxFactory;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_AdminNotification_Model_InboxFactory $inboxFactory
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_AdminNotification_Model_InboxFactory $inboxFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_inboxFactory = $inboxFactory;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setSaveParametersInSession(true);
        $this->setId('notificationGrid');
        $this->setIdFieldName('notification_id');
        $this->setDefaultSort('date_added', 'desc');
        $this->setFilterVisibility(false);
    }

    /**
     * Init backups collection
     */
    protected function _prepareCollection()
    {
        $collection = $this->_inboxFactory->create()
            ->getCollection()
            ->addRemoveFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        /** @var $helper Mage_AdminNotification_Helper_Data */
        $helper = $this->helper('Mage_AdminNotification_Helper_Data');
        $this->addColumn('severity', array(
            'header'    =>  $helper->__('Severity'),
            'width'     => '60px',
            'index'     => 'severity',
            'renderer'  => 'Mage_AdminNotification_Block_Grid_Renderer_Severity',
        ));

        $this->addColumn('date_added', array(
            'header'    => $helper->__('Added'),
            'index'     => 'date_added',
            'width'     => '150px',
            'type'      => 'datetime'
        ));

        $this->addColumn('title', array(
            'header'    => $helper->__('Message'),
            'index'     => 'title',
            'renderer'  => 'Mage_AdminNotification_Block_Grid_Renderer_Notice',
        ));

        $this->addColumn('actions', array(
            'header'    => $helper->__('Actions'),
            'width'     => '250px',
            'sortable'  => false,
            'renderer'  => 'Mage_AdminNotification_Block_Grid_Renderer_Actions',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action
     */
    protected function _prepareMassaction()
    {
        /** @var $helper Mage_AdminNotification_Helper_Data */
        $helper = $this->_helperFactory->get('Mage_AdminNotification_Helper_Data');

        $this->setMassactionIdField('notification_id');
        $this->getMassactionBlock()->setFormFieldName('notification');

        $this->getMassactionBlock()->addItem('mark_as_read', array(
             'label'    => $helper->__('Mark as Read'),
             'url'      => $this->getUrl('*/*/massMarkAsRead', array('_current'=>true)),
        ));

        $this->getMassactionBlock()->addItem('remove', array(
             'label'    => $helper->__('Remove'),
             'url'      => $this->getUrl('*/*/massRemove'),
             'confirm'  => $helper->__('Are you sure?')
        ));
        return $this;
    }

    public function getRowClass(Varien_Object $row)
    {
        return $row->getIsRead() ? 'read' : 'unread';
    }

    public function getRowClickCallback()
    {
        return false;
    }
}
