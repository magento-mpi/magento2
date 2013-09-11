<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme selector tab for customized themes
 */
namespace Magento\DesignEditor\Block\Adminhtml\Theme\Selector\Tab;

class Customizations
    extends \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\Tab\TabAbstract
{
    /**
     * Initialize tab block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setActive(true);
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('My Customizations');
    }
}
