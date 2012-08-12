<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter problem grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Newsletter_Problem_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('problemGrid');
        $this->setSaveParametersInSession(true);
        $this->setMessageBlockVisibility(true);
        $this->setUseAjax(true);
        $this->setEmptyText(Mage::helper('Mage_Newsletter_Helper_Data')->__('No problems found.'));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Newsletter_Model_Resource_Problem_Collection')
            ->addSubscriberInfo()
            ->addQueueInfo();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('checkbox', array(
             'sortable' 	=> false,
            'filter'	=> 'Mage_Adminhtml_Block_Newsletter_Problem_Grid_Filter_Checkbox',
            'renderer'	=> 'Mage_Adminhtml_Block_Newsletter_Problem_Grid_Renderer_Checkbox',
            'width'		=> '20px'
        ));

        $this->addColumn('problem_id', array(
            'header' => Mage::helper('Mage_Newsletter_Helper_Data')->__('ID'),
            'index'  => 'problem_id',
            'width'	 => '50px'
        ));

        $this->addColumn('subscriber', array(
            'header' => Mage::helper('Mage_Newsletter_Helper_Data')->__('Subscriber'),
            'index'  => 'subscriber_id',
            'format' => '#$subscriber_id $customer_name ($subscriber_email)'
        ));

        $this->addColumn('queue_start', array(
            'header' => Mage::helper('Mage_Newsletter_Helper_Data')->__('Queue Date Start'),
            'index'  => 'queue_start_at',
            'gmtoffset' => true,
            'type'	 => 'datetime'
        ));

        $this->addColumn('queue', array(
            'header' => Mage::helper('Mage_Newsletter_Helper_Data')->__('Queue Subject'),
            'index'  => 'template_subject'
        ));

        $this->addColumn('problem_code', array(
            'header' => Mage::helper('Mage_Newsletter_Helper_Data')->__('Error Code'),
            'index'  => 'problem_error_code',
            'type'   => 'number'
        ));

        $this->addColumn('problem_text', array(
            'header' => Mage::helper('Mage_Newsletter_Helper_Data')->__('Error Text'),
            'index'  => 'problem_error_text'
        ));
        return parent::_prepareColumns();
    }
}// Class Mage_Adminhtml_Block_Newsletter_Problem_Grid END
