<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth consumers grid container block
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Block_Adminhtml_Oauth_Consumer extends Magento_Backend_Block_Widget_Grid_Container
{
    /**
     * Construct grid container
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Magento_Oauth';
        $this->_controller = 'adminhtml_oauth_consumer';
        $this->_headerText = __('Add-Ons');

        if (!$this->_authorization->isAllowed('Magento_Oauth::consumer_edit')) {
            $this->_removeButton('add');
        }
    }
}
