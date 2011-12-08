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
 * Xmlconnect form date element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Simplexml_Form_Element_Date
    extends Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract
{
    /**
     * Init multiline element
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('date');
    }

    /**
     * Required element attribute array
     *
     * @return array
     */
    public function getRequiredXmlAttributes()
    {
        return array(
            'label' => null,
            'type' => null,
            'format' => null
        );
    }

    /**
     * Add value to element
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract
     */
    protected function _addValue(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        $values = $this->getEscapedValue();
        if (!empty($values)) {
            $valuesXmlObj = $xmlObj->addCustomChild('values');
            foreach ($values as $element => $config) {
                $valuesXmlObj->addCustomChild('item', null, array(
                    'id' => $config['id'],
                    'title' => $config['title'],
                    'label' => $config['label'],
                    'type' => $element,
                    'value' => $config['value']
                ));
            }
        }
        return $this;
    }
}
