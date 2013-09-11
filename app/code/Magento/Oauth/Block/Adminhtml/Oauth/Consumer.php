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
namespace Magento\Oauth\Block\Adminhtml\Oauth;

class Consumer extends \Magento\Adminhtml\Block\Widget\Grid\Container
{
    /**
     * Construct grid container
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Magento_Oauth';
        $this->_controller = 'adminhtml_oauth_consumer';
        $this->_headerText = __('OAuth Consumers');

        //check allow edit
        if (!$this->_authorization->isAllowed('Magento_Oauth::consumer_edit')) {
            $this->_removeButton('add');
        }
    }
}
