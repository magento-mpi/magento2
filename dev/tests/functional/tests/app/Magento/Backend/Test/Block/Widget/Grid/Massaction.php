<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Widget\Grid;

use Mtf\Block\Form;
use Mtf\Client\Element;

/**
 * Class Massaction
 * Mass action grid form
 */
class Massaction extends Form
{
    /**
     * CSS selector for button "submit" a form
     *
     * @var string
     */
    protected $submitButton = '[type="button"][title="Submit"]';

    /**
     * Filling form
     *
     * @param array $fields
     * @param Element|null $element [optional]
     * @return $this
     */
    public function fillData(array $fields, Element $element = null)
    {
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping, $element);

        return $this;
    }

    /**
     * Submit form
     *
     * @param bool $accept [optional]
     * @return void
     */
    public function submit($accept = true)
    {
        $this->_rootElement->find($this->submitButton)->click();
        if ($accept) {
            $this->_rootElement->acceptAlert();
        }
    }
}
