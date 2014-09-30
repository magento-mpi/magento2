<?php
/**
* {license_notice}
*
* @copyright   {copyright}
* @license     {license_link}
*/
namespace Magento\Ui\Component\Form;

use Magento\Ui\Component\AbstractView;

/**
 * Class Fieldset
 */
class Fieldset extends AbstractView
{
    /**
     * @var array
     */
    protected $elements = [];

    public function getElement($key)
    {
        return isset($this->elements[$key]) ? $this->elements[$key] : null;
    }

    public function getElements()
    {
        return $this->elements;
    }

    public function addElement($name, $data)
    {

    }

    public function removeElement()
    {
        //
    }
}
