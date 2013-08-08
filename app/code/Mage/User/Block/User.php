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
class Mage_User_Block_User extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * @var Mage_User_Model_Resource_User
     */
    protected $_resourceModel;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_User_Model_Resource_User $resourceModel
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_User_Model_Resource_User $resourceModel,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_resourceModel = $resourceModel;
    }

    protected function _construct()
    {
        $this->addData(array(
            Mage_Backend_Block_Widget_Container::PARAM_CONTROLLER => 'user',
            Mage_Backend_Block_Widget_Grid_Container::PARAM_BLOCK_GROUP => 'Mage_User',
            Mage_Backend_Block_Widget_Grid_Container::PARAM_BUTTON_NEW => __('Add New User'),
            Mage_Backend_Block_Widget_Container::PARAM_HEADER_TEXT => __('Users'),
        ));
        parent::_construct();
        $this->_addNewButton();
    }
}
