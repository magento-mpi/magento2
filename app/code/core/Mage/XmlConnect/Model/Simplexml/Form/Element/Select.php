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
 * Xmlconnect form select element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Simplexml_Form_Element_Select
    extends Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract
{
    /**
     * Init text element
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('select');
    }

    /**
     * Add value and options to select
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Form_Element_Select
     */
    protected function _addValue(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        $value = $this->getEscapedValue();
        if ($value !== null) {
            $xmlObj->addAttribute(
                'value',
                $xmlObj->xmlAttribute($value)
            );
        }
        $this->_addOptions($xmlObj);

        return $this;
    }

    /**
     * Add options to select
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Form_Element_Select
     */
    protected function _addOptions(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        if ($this->getOptions() && is_array($this->getOptions())) {
            $valuesXmlObj = $xmlObj->addCustomChild('values');
            foreach ($this->getOptions() as $option) {

                if (!isset($option['value']) || $option['value'] == '') {
                    continue;
                }

                $valuesXmlObj->addCustomChild('item', null, array(
                    'label' => $option['label'],
                    'value' => $option['value']
                ));
            }
        }
    }
}
