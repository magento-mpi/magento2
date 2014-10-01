<?php
/**
* {license_notice}
*
* @copyright   {copyright}
* @license     {license_link}
*/
namespace Magento\Ui\Component\Form;

use Magento\Ui\Component\AbstractView;
use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class Fieldset
 */
class Fieldset extends AbstractView
{
    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @return UiComponentInterface[]
     */
    public function getElements()
    {
        $this->initElements();
        return $this->elements;
    }

    /**
     * @return void
     */
    protected function initElements()
    {
        foreach ($this->getDataProviders() as $dataProviderInstance) {
        }
    }

    protected function getDataProviders()
    {
        return (array)$this->getData('data_provider');
    }

    public function addElement($element)
    {
        $this->elements[] = $element;
    }
}
