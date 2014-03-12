<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Block\Adminhtml\System\Design;

/**
 *  Container for theme grid
 */
class Theme extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize grid container and prepare controls
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'Magento_Theme';
        $this->_controller = 'Adminhtml_System_Design_Theme';
        if (is_object($this->getLayout()->getBlock('page-title'))) {
            $this->getLayout()->getBlock('page-title')->setPageTitle('Themes');
        }
        
        $this->_updateButton('add', 'label', __('Add New Theme'));
    }

    /**
     * Prepare header for container
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Themes');
    }
}
