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
class RuleInformation extends Tab
{
    private $fieldPrefix = '#rule_';

    public function fillFormTab(array $fields, Element $element)
    {
        foreach ($fields as $key => $value) {
            $this->_mapping[$key] = $this->fieldPrefix . $key;
        }
        parent::fillFormTab($fields, $element);
    }
}
