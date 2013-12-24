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

namespace Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Customersegment\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class General
 * Customer segment form block
 *
 * @package Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Customersegment\Edit\Tab
 */
class General extends Tab
{
    /**
     * prefix name in the id to identify the fields to fill
     *
     * @var string
     */
    private $fieldPrefix = 'segment_';

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
