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

namespace Magento\Customer\Test\Block\Adminhtml\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Account
 * Customer account form block
 *
 * @package Magento\Customer\Test\Block\Adminhtml\Edit\Tab
 */
class Account extends Tab
{
    protected $fieldPrefix = '_account';

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
