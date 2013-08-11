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
 * Adminhtml permissions user block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Api_User extends Mage_Backend_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'api_user';
        $this->_headerText = __('Users');
        $this->_addButtonLabel = __('Add New User');
        parent::_construct();
    }

    /**
     * Prepare output HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('api_user_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}
