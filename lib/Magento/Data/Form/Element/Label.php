<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Data form abstract class
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Form_Element_Label extends Magento_Data_Form_Element_Abstract
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
        $html = $this->getBold() ? '<div class="control-value special">' : '<div class="control-value">';
        $html.= $this->getEscapedValue();
        $html.= $this->getBold() ? '</div>' : '</div>';
        $html.= $this->getAfterElementHtml();
        return $html;
    }
}
