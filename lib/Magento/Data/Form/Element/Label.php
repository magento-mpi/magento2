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
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param array $attributes
     */
    public function __construct(
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Data_Form_Element_CollectionFactory $factoryCollection,
        $attributes = array()
    ) {
        parent::__construct($factoryElement, $factoryCollection, $attributes);
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
