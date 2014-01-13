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

namespace Magento\Cms\Test\Block\AdminHtml\Page\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Main
 * Cms Page New form block
 *
 * @package Magento\Cms\Test\Block\AdminHtml\Page\Edit\Tab
 */
class Main extends Tab
{
    /**
     * prefix name in the id to identify the fields to fill
     *
     * @var string
     */
    private $fieldPrefix = 'page_';

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
