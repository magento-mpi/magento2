<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Widget;

use Mtf\Block\Form as AbstractForm;
use Mtf\Client\Element;

/**
 * Class Tab
 * Is used to represent any tab on the page
 *
 * @package Magento\Backend\Test\Block\Widget
 */
class Tab extends AbstractForm
{
    /**
     * Fill data to fields on tab
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        $data = $this->dataMapping($fields);
        $this->_fill($data, $element);

        return $this;
    }

    /**
     * Verify data to fields on tab
     *
     * @param array $fields
     * @param Element $element
     *
     * @return bool
     */
    public function verifyFormTab(array $fields, Element $element)
    {
        $data = $this->dataMapping($fields);
        return $this->_verify($data, $element);
    }

    /**
     * Update data to fields on tab
     *
     * @param array $fields
     * @param Element $element
     */
    public function updateFormTab(array $fields, Element $element)
    {
        $this->fillFormTab($fields, $element);
    }
}
