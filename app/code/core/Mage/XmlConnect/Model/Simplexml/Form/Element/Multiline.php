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
 * Xmlconnect form multiline element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Simplexml_Form_Element_Multiline
    extends Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract
{
    /**
     * Format for Xml elements id attribute
     *
     * @var string
     */
    protected $_fieldIdFormat   = '%1$s';

    /**
     * Format for Xml elements name attribute
     *
     * @var string
     */
    protected $_fieldNameFormat = '%1$s';

    /**
     * Init multiline element
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        if (!isset($attributes['line_count'])) {
            Mage::throwException(
                Mage::helper('Mage_XmlConnect_Helper_Data')->__('"line_count" attribute is required for "multiline" element.')
            );
        }
        parent::__construct($attributes);
        $this->setType('multiline');
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
            'type' => null
        );
    }

    /**
     * Return Xml id for element
     *
     * @param null|string $index
     * @return string
     */
    public function getXmlId($index = null)
    {
        $format = $this->_fieldIdFormat;
        if (!is_null($index)) {
            $format .= '_%2$s';
        }
        return sprintf($format, $this->getData('attribute_code'), $index);
    }

    /**
     * Return Xml id for element
     *
     * @param null|string $index
     * @return string
     */
    public function getFieldName($index = null)
    {
        $format = $this->_fieldNameFormat;
        if (!is_null($index)) {
            $format .= '[%2$s]';
        }
        return sprintf($format, $this->getData('attribute_code'), $index);
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
            for ($i = 0; $i < $this->getData('line_count'); $i++) {
                $value = !empty($values[$i]) ? array('value' => $values[$i]) : array();

                $valuesXmlObj->addCustomChild('item', null, array(
                    'id' => $this->getXmlId($i),
                    'name' => $this->getFieldName($i)
                ) + $value);
            }
        }
        return $this;
    }
}
