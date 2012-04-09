<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Oauth
 */

/**
 * OAuth authorized tokens grid container block
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Block_Adminhtml_Oauth_AuthorizedTokens extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct grid container
     */
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'oauth';
        $this->_controller = 'adminhtml_oauth_authorizedTokens';
        $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Authorized OAuth Tokens');

        $this->_removeButton('add');
    }
}

