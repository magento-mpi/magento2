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
 * XmlConnect application grid
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_mobile';
        $this->_blockGroup = 'Mage_XmlConnect';
        $xmlconnectVersion = Mage::getConfig()->getNode(
            Mage_XmlConnect_Model_Application::XML_PATH_MODULE_VERSION
        );
        $this->_headerText = $this->__('Manage Apps')
            . ' '
            . $this->__('ver. %s', $xmlconnectVersion);
        $this->_addButtonLabel = $this->__('Add App');

        parent::_construct();
    }
}
