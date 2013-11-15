<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Block\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class RuleConditions extends Tab
{
    private $fieldPrefix = '#conditions__1__';

    public function fillFormTab(array $fields, Element $element)
    {
        foreach ($fields as $key => $value) {
            $this->_mapping[$key] = $this->fieldPrefix . $key;
        }
        parent::fillFormTab($fields, $element);
    }
}
