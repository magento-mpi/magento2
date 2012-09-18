<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API permissions user block
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_User extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * Initialize grid container
     */
    public function __construct()
    {
        $this->_blockGroup = 'Mage_Webapi';
        $this->_controller = 'adminhtml_user';
        $this->_headerText = Mage::helper('Mage_Webapi_Helper_Data')->__('API Users');
        $this->_addButtonLabel = Mage::helper('Mage_Webapi_Helper_Data')->__('Add New API User');
        parent::__construct();
    }

    /**
     * Prepare output HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('webapi_user_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}
