<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Main
 *
 * @package Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class Main extends Tab
{
    /**
     * Field Prefix
     */
    const FIELD_PREFIX = '#rule_';

    /**
     * Group Name Constant
     */
    const GROUP = 'promo_catalog_edit_tabs_main_section_content';

    /**
     * Map fields and fill form
     *
     * @param array $fields
     * @param Element $element
     */
    public function fillFormTab(array $fields, Element $element)
    {
        foreach ($fields as $key => $value) {
            $this->_mapping[$key] = self::FIELD_PREFIX . $key;
        }
        parent::fillFormTab($fields, $element);
    }
}
