<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Attribute Edit block
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Block_Adminhtml_Attribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize edit form container
     */
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'api2';
        $this->_controller = 'adminhtml_attribute';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save'))
            ->_removeButton('delete');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        $userTypes = Mage_Api2_Model_Auth_User::getUserTypes();
        return $this->__('Edit attribute rules for %s Role', $userTypes[$this->getRequest()->getParam('type')]);
    }
}
