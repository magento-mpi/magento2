<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Block\Adminhtml;

/**
 * Adminhtml cms pages content block
 */
class Page extends \Magento\Ui\Listing\Block\Container
{
    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_page';
        $this->_blockGroup = 'Magento_Cms';
        $this->_headerText = __('Manage Pages');

        parent::_construct();

        if ($this->_isAllowedAction('Magento_Cms::save')) {
            $this->buttonList->update('add', 'label', __('Add New Page'));
        } else {
            $this->buttonList->remove('add');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
