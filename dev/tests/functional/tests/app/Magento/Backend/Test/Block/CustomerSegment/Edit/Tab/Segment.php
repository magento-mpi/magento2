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

namespace Magento\Backend\Test\Block\CustomerSegment\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Segment
 * Customer segment form block
 *
 * @package Magento\Backend\Test\Block\CustomerSegment\Tab
 */
class Segment extends Tab {
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