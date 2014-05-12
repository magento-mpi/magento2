<?php
/**
 * {license_notice}
 *
 * @api
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
 */
class Tab extends AbstractForm
{
    /**
     * Fill data to fields on tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $data = $this->dataMapping($fields);
        $this->_fill($data, $element);

        return $this;
    }

    /**
     * Get data of tab
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $data = $this->dataMapping($fields);
        return $this->_getData($data, $element);
    }

    /**
     * Update data to fields on tab
     *
     * @param array $fields
     * @param Element|null $element
     */
    public function updateFormTab(array $fields, Element $element = null)
    {
        $this->fillFormTab($fields, $element);
    }
}
