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
     * Legend text
     *
     * @var string
     */
    protected $legendText = 'Legend text';

    /**
     * @return string
     */
    public function getLegendText()
    {
        return $this->legendText;
    }

    /**
     * Set legend text
     *
     * @param $legendText
     * @return void
     */
    public function setLegendText($legendText)
    {
        $this->legendText = $legendText;
    }

    /**
     * Prepare component data
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        $this->elements = $this->getData('elements');
    }
}
