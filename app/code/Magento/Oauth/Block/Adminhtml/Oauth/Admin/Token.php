<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth consumers grid container block
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Oauth\Block\Adminhtml\Oauth\Admin;

class Token extends \Magento\Adminhtml\Block\Widget\Grid\Container
{
    /**
     * Construct grid container
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Magento_Oauth';
        $this->_controller = 'adminhtml_oauth_admin_token';
        $this->_headerText = __('My Applications');
        $this->_removeButton('add');
    }
}
