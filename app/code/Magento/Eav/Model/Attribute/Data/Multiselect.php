<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Entity Attribute Multiply select Data Model
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Attribute\Data;

class Multiselect extends \Magento\Eav\Model\Attribute\Data\Select
{
    /**
     * Extract data from request and return value
     *
     * @param \Zend_Controller_Request_Http $request
     * @return array|string
     */
    public function extractValue(\Zend_Controller_Request_Http $request)
    {
        $values = $this->_getRequestValue($request);
        if ($values !== false && !is_array($values)) {
            $values = array($values);
        }
        return $values;
    }

    /**
     * Export attribute value to entity model
     *
     * @param array|string $value
     * @return \Magento\Eav\Model\Attribute\Data\Multiselect
     */
    public function compactValue($value)
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }
        return parent::compactValue($value);
    }

    /**
     * Return formated attribute value from entity model
     *
     * @param string $format
     * @return array|string
     */
    public function outputValue($format = \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_TEXT)
    {
        $values = $this->getEntity()->getData($this->getAttribute()->getAttributeCode());
        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        switch ($format) {
            case \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_JSON:
            case \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_ARRAY:
                $output = $values;
            default:
                $output = array();
                foreach ($values as $value) {
                    if (!$value) {
                        continue;
                    }
                    $output[] = $this->getAttribute()->getSource()->getOptionText($value);
                }
                $output = implode(', ', $output);
                break;
        }

        return $output;
    }
}
