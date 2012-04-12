<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
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
class Mage_Oauth_Block_Adminhtml_Oauth_Consumer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct grid container
     */
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'oauth';
        $this->_controller = 'adminhtml_oauth_consumer';
        $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('OAuth Consumers');

        //check allow edit
        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('Mage_Admin_Model_Session');
        if (!$session->isAllowed('system/oauth/consumer/edit')) {
            $this->_removeButton('add');
        }
    }
}
