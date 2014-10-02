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
    public function prepare()
    {
        parent::prepare();
        $this->elements = $this->getData('elements');
    }
}
