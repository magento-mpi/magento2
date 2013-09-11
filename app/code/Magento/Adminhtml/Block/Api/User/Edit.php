<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml permissions user edit page
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Api\User;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{

    protected function _construct()
    {
        $this->_objectId = 'user_id';
        $this->_controller = 'api_user';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save User'));
        $this->_updateButton('delete', 'label', __('Delete User'));
    }

    public function getHeaderText()
    {
        if (\Mage::registry('api_user')->getId()) {
            return __("Edit User '%1'", $this->escapeHtml(\Mage::registry('api_user')->getUsername()));
        }
        else {
            return __('New User');
        }
    }

}
