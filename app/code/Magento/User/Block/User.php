<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User block
 *
 * @category   Magento
 * @package    Magento_User
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_User_Block_User extends Magento_Backend_Block_Widget_Grid_Container
{
    /**
     * @var Magento_User_Model_Resource_User
     */
    protected $_resourceModel;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_User_Model_Resource_User $resourceModel
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_User_Model_Resource_User $resourceModel,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_resourceModel = $resourceModel;
    }

    protected function _construct()
    {
        $this->addData(array(
            Magento_Backend_Block_Widget_Container::PARAM_CONTROLLER => 'user',
            Magento_Backend_Block_Widget_Grid_Container::PARAM_BLOCK_GROUP => 'Magento_User',
            Magento_Backend_Block_Widget_Grid_Container::PARAM_BUTTON_NEW => __('Add New User'),
            Magento_Backend_Block_Widget_Container::PARAM_HEADER_TEXT => __('Users'),
        ));
        parent::_construct();
        $this->_addNewButton();
    }
}
