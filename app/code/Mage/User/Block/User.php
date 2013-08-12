<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User block
 *
 * @category   Mage
 * @package    Mage_User
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Block_User extends Magento_Backend_Block_Widget_Grid_Container
{
    /**
     * @var Mage_User_Model_Resource_User
     */
    protected $_resourceModel;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Mage_User_Model_Resource_User $resourceModel
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Mage_User_Model_Resource_User $resourceModel,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_resourceModel = $resourceModel;
    }

    protected function _construct()
    {
        $this->addData(array(
            Magento_Backend_Block_Widget_Container::PARAM_CONTROLLER => 'user',
            Magento_Backend_Block_Widget_Grid_Container::PARAM_BLOCK_GROUP => 'Mage_User',
            Magento_Backend_Block_Widget_Grid_Container::PARAM_BUTTON_NEW => $this->__('Add New User'),
            Magento_Backend_Block_Widget_Container::PARAM_HEADER_TEXT => $this->__('Users'),
        ));
        parent::_construct();
        $this->_addNewButton();
    }
}
