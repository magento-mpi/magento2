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
 * Web API Adminhtml role block
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Webapi_Block_Adminhtml_Role extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'Mage_Webapi';
        $this->_controller = 'adminhtml_role';
        $this->_headerText = Mage::helper('Mage_Webapi_Helper_Data')->__('API Roles');
        $this->_addButtonLabel = Mage::helper('Mage_Webapi_Helper_Data')->__('Add New Role');

        parent::__construct();
    }

    /**
     * Get create URL
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/edit');
    }
}
