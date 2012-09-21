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
 * Adminhtml permissioms role block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Webapi_Block_Adminhtml_Role extends Mage_Backend_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'Mage_Webapi';
        $this->_controller = 'adminhtml_role';
        $this->_headerText = Mage::helper('Mage_Webapi_Helper_Data')->__('API Roles');
        $this->_addButtonLabel = Mage::helper('Mage_Webapi_Helper_Data')->__('Add New Role');

        parent::__construct();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/editrole');
    }
}
