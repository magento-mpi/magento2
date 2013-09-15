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
namespace Magento\User\Block;

class User extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var \Magento\User\Model\Resource\User
     */
    protected $_resourceModel;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param \Magento\User\Model\Resource\User $resourceModel
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\User\Model\Resource\User $resourceModel,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_resourceModel = $resourceModel;
    }

    protected function _construct()
    {
        $this->addData(array(
            \Magento\Backend\Block\Widget\Container::PARAM_CONTROLLER => 'user',
            \Magento\Backend\Block\Widget\Grid\Container::PARAM_BLOCK_GROUP => 'Magento_User',
            \Magento\Backend\Block\Widget\Grid\Container::PARAM_BUTTON_NEW => __('Add New User'),
            \Magento\Backend\Block\Widget\Container::PARAM_HEADER_TEXT => __('Users'),
        ));
        parent::_construct();
        $this->_addNewButton();
    }
}
