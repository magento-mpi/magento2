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
 * User edit page
 *
 * @category   Magento
 * @package    Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\User\Block\User;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'user_id';
        $this->_controller = 'user';
        $this->_blockGroup = 'Magento_User';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save User'));
        $this->_updateButton('delete', 'label', __('Delete User'));
    }

    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('permissions_user')->getId()) {
            $username = $this->escapeHtml($this->_coreRegistry->registry('permissions_user')->getUsername());
            return __("Edit User '%1'", $username);
        } else {
            return __('New User');
        }
    }

}
