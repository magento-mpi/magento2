<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Xmlconnect form multiselect element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Simplexml_Form_Element_Multiselect
    extends Mage_XmlConnect_Model_Simplexml_Form_Element_Select
{
    /**
     * Init text element
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('multiselect');
    }

    /**
     * Add value to element
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Form_Element_Multiselect
     */
    protected function _addValue(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        $values = array();
        if (is_array($this->getEscapedValue())) {
            $values = $this->getEscapedValue();
        }

        $valuesXmlObj = $xmlObj->addCustomChild('values');
        foreach ($this->getOptions() as $option) {

            if (empty($option['value'])) {
                continue;
            }

            $selected = array();
            if (in_array($option['value'], $values)) {
                $selected = array('selected' => 1);
            }

            $valuesXmlObj->addCustomChild('item', null, array(
                'label' => $option['label'],
                'value' => $option['value']
            ) + $selected);
        }

        return $this;
    }
}
