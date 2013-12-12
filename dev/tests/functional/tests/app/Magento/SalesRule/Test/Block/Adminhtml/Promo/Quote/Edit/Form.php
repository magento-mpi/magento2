<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit;

use Magento\Backend\Test\Block\Widget\FormTabs;

class Form extends FormTabs
{
    const RULE_MAIN_TAB = 'promo_catalog_edit_tabs_main_section_content';

    const RULE_COND_TAB = 'promo_catalog_edit_tabs_conditions_section_content';

    const RULE_ACTIONS_TAB = 'promo_catalog_edit_tabs_actions_section_content';

    protected $tabClasses = array(
        self::RULE_MAIN_TAB => '\\Magento\\SalesRule\\Test\\Block\\Adminhtml\\Promo\\Quote\\Edit\\Tab\\Main',
        self::RULE_COND_TAB => '\\Magento\\SalesRule\\Test\Block\\Adminhtml\\Promo\\Quote\\Edit\\Tab\\Conditions',
        self::RULE_ACTIONS_TAB => '\\Magento\\SalesRule\\Test\\Block\\Adminhtml\\Promo\\Quote\\Edit\\Tab\\Main'
    );

    /**
     * Click save button on form
     */
    public function clickSave()
    {
        $this->_rootElement->find('#save')->click();
    }
}
