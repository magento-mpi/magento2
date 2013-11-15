<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Block;

use Magento\Backend\Test\Block\Widget\FormTabs;

class PromoQuoteForm extends FormTabs
{
    const RULE_INFO_TAB = 'promo_catalog_edit_tabs_main_section_content';
    const RULE_COND_TAB = 'promo_catalog_edit_tabs_conditions_section_content';

    protected $_tabClasses = [
        self::RULE_INFO_TAB =>
            '\\Magento\\SalesRule\\Test\\Block\\Edit\\Tab\\RuleInformation',
        self::RULE_COND_TAB =>
            '\\Magento\\SalesRule\\Test\\Block\\Edit\\Tab\\RuleConditions'
    ];

    /**
     * Click save button on form
     */
    public function clickSave()
    {
        $this->_rootElement->find('#save')->click();
    }
}
