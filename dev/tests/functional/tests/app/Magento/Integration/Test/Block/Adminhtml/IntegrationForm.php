<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Integration form block.
 */
class IntegrationForm extends FormTabs
{
    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        parent::_init();
        $this->_tabClasses = array(
            'integration_edit_tabs_info_section_content' =>
                '\\Magento\\Integration\\Test\\Block\\Adminhtml\\Integration\\Edit\\Tab\\Info',
        );
    }
}
