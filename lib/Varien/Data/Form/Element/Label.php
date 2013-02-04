<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Data form abstract class
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Label extends Varien_Data_Form_Element_Abstract
{
    /**
     * Assigns attributes for Element
     *
     * @param array $attributes
     */
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('label');
    }

    /**
     * Retrieve Element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = $this->getBold() ? '<strong class="control-value">' : '<span class="control-value">';
        $html.= $this->getEscapedValue();
        $html.= $this->getBold() ? '</strong>' : '</span>';
        $html.= $this->getAfterElementHtml();
        return $html;
    }
}
