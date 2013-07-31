<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Adminhtml
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth consumers grid container block
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Admin_Token extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct grid container
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Mage_Oauth';
        $this->_controller = 'adminhtml_oauth_admin_token';
        $this->_headerText = Mage::helper('Magento_Adminhtml_Helper_Data')->__('My Applications');
        $this->_removeButton('add');
    }
}
