<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType;

use Mtf\Client\Element;
use Mtf\Block\Form;

/**
 * Class LayoutForm
 * Responds for filling layout form
 */
abstract class LayoutForm extends Form
{
    /**
     * Widget option chooser button
     *
     * @var string
     */
    protected $chooser = '.widget-option-chooser';

    /**
     * Filling attribute form
     *
     * @param array $layoutFields
     * @param Element $element
     * @return void
     */
    public function fillForm(array $layoutFields, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($layoutFields);
        $this->_fill($mapping, $element);
    }

    /**
     * Getting options data form on the product form
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function getDataOptions(array $fields = null, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($fields);
        return $this->_getData($mapping, $element);
    }
}
