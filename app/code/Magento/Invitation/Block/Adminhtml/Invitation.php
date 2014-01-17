<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation Adminhtml Block
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Block\Adminhtml;

class Invitation extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize invitation manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_invitation';
        $this->_blockGroup = 'Magento_Invitation';
        $this->_headerText = __('Invitations');
        $this->_addButtonLabel = __('Add Invitations');
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getHeaderCssClass() {
        return 'icon-head head-invitation';
    }

}
