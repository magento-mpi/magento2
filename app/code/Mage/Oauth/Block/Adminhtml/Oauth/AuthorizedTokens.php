<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth authorized tokens grid container block
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Block_Adminhtml_Oauth_AuthorizedTokens extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct grid container
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Mage_Oauth';
        $this->_controller = 'adminhtml_oauth_authorizedTokens';
        $this->_headerText = Mage::helper('Magento_Adminhtml_Helper_Data')->__('Authorized OAuth Tokens');

        $this->_removeButton('add');
    }
}

