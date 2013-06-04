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
 * Adminhtml store content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Store_Store extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Mage_Adminhtml';

    /**
     * @var Mage_Core_Model_Store_Group_Limitation
     */
    protected $_storeGroupLimitation;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_Store_Group_Limitation $storeGroupLimitation
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_Store_Group_Limitation $storeGroupLimitation,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_storeGroupLimitation = $storeGroupLimitation;
    }

    protected function _construct()
    {
        $this->_controller  = 'system_store';
        $this->_headerText  = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Manage Stores');
        parent::_construct();
    }

    protected function _prepareLayout()
    {
        /* Update default add button to add website button */
        $this->_updateButton('add', 'label', Mage::helper('Mage_Core_Helper_Data')->__('Create Website'));
        $this->_updateButton('add', 'onclick', "setLocation('" . $this->getUrl('*/*/newWebsite') . "')");

        /* Add Store Group button */
        if ($this->_storeGroupLimitation->canCreate()) {
            $this->_addButton('add_group', array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Create Store'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/newGroup') .'\')',
                'class'     => 'add',
            ));
        }

        /* Add Store button */
        $this->_addButton('add_store', array(
            'label'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Create Store View'),
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/newStore') . '\')',
            'class'   => 'add',
        ));

        return parent::_prepareLayout();
    }
}
