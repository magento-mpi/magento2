<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Customer\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Account
 * Customer account form block
 *
 * @package Magento\Backend\Test\Block\Customer\Tab
 */
class Account extends Tab
{
    private $fieldPrefix = '_account';

    /**
     * Fill data to fields on tab
     *
     * @param array $fields
     * @param Element $element
     */
    public function fillFormTab(array $fields, Element $element)
    {
        foreach ($fields as $key => $value) {
            $this->_mapping[$key] = '#' . $this->fieldPrefix . $key;
        }
        parent::fillFormTab($fields, $element);
    }
}
