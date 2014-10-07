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
    protected $legendText = '';

    /**
     * @var bool
     */
    protected $collapsible = false;

    /**
     * @var bool
     */
    protected $loadByAjax = false;

    /**
     * @return void
     */
    public function setNotLoadByAjax()
    {
        $this->loadByAjax = false;
    }

    /**
     * @return string
     */
    public function getLegendText()
    {
        return $this->legendText;
    }

    /**
     * @return bool
     */
    public function getIsCollapsible()
    {
        return $this->collapsible;
    }

    /**
     * @return bool
     */
    public function getIsAjax()
    {
        return $this->loadByAjax;
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('mui/form/fieldset');
    }

    /**
     * Prepare component data
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        $this->legendText = $this->getData('label') ? $this->getData('label') : $this->legendText;
        $this->elements = $this->getData('elements') ?: $this->elements;
        $this->collapsible = $this->getData('collapsible') ?: $this->collapsible;
        $this->loadByAjax = $this->getData('load_by_ajax') ?: $this->loadByAjax;
    }
}
