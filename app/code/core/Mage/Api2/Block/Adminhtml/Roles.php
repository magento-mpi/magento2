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
 * Roles grid container block
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Block_Adminhtml_Roles extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct grid container
     */
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'Mage_Api2';
        $this->_controller = 'adminhtml_roles';
        $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('REST Roles');

        //check allow edit
        /** @var $session Mage_Backend_Model_Auth_Session */
        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        if ($session->isAllowed('system/api/roles/add')) {
            $this->_updateButton('add', 'label', $this->__('Add Admin Role'));
        } else {
            $this->_removeButton('add');
        }
    }
}
