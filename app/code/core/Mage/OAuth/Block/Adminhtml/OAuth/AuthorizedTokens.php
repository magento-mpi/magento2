<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_OAuth
 */

/**
 * OAuth authorized tokens grid container block
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Block_Adminhtml_OAuth_AuthorizedTokens extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct grid container
     */
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'oauth';
        $this->_controller = 'adminhtml_oAuth_authorizedTokens';
        $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Authorized OAuth Tokens');

        $this->_removeButton('add');
    }
}

