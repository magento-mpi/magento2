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
 * XmlConnect AirMail message queue grid
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Queue extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mage_XmlConnect';
        $this->_controller = 'adminhtml_queue';
        $this->_headerText = $this->__('AirMail Messages Queue');

        parent::_construct();
        $this->removeButton('add');
    }
}
