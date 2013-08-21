<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth authorized tokens grid container block
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Block_Adminhtml_Oauth_AuthorizedTokens extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct grid container
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Magento_Oauth';
        $this->_controller = 'adminhtml_oauth_authorizedTokens';
        $this->_headerText = __('Authorized OAuth Tokens');

        $this->_removeButton('add');
    }
}

