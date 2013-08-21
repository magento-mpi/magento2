<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation Adminhtml Block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation extends Magento_Backend_Block_Widget_Grid_Container
{
    /**
     * Initialize invitation manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_invitation';
        $this->_blockGroup = 'Enterprise_Invitation';
        $this->_headerText = __('Invitations');
        $this->_addButtonLabel = __('Add Invitations');
        parent::_construct();
    }

    public function getHeaderCssClass() {
        return 'icon-head head-invitation';
    }

}
