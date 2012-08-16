<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect AirMail message template grid
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Template extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mage_XmlConnect';
        $this->_controller = 'adminhtml_template';
        $this->_headerText = $this->__('AirMail templates');

        parent::_construct();
        $this->removeButton('add');
    }

    /**
     * Prepare layout
     * Add new button
     *
     * @return Mage_Adminhtml_Block_Widget_Grid_Container
     */
    protected function _prepareLayout()
    {
        $this->_addButton('add_new', array(
            'label'   => $this->__('Add New Template'),
            'onclick' => "setLocation('{$this->getUrl('*/*/newTemplate')}')",
            'class'   => 'add'
        ));

        return parent::_prepareLayout();
    }
}
