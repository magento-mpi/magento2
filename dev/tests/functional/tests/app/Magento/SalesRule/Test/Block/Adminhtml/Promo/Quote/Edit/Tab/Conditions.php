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
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class Conditions extends Tab
{
    const FIELD_PREFIX = '#conditions__1__';

    public function fillFormTab(array $fields, Element $element)
    {
        foreach ($fields as $key => $value) {
            $this->_mapping[$key] = self::FIELD_PREFIX . $key;
        }
        parent::fillFormTab($fields, $element);
    }
}
