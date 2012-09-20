<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Webapi renderer for XML format.
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Controller_Response_Renderer_Xml implements Mage_Webapi_Controller_Response_RendererInterface
{
    /**
     * Adapter mime type.
     */
    const MIME_TYPE = 'application/xml';

    /**
     * Default name for item in indexed array.
     */
    const DEFAULT_INDEXED_ARRAY_ITEM_NAME = 'data_item';

    /**
     * Characters for replacement in the tag name.
     *
     * @var array
     */
    protected $_tagNameReplacementCharMap = array(
        '!' => '', '"' => '', '#' => '', '$' => '', '%' => '', '&' => '', '\'' => '',
        '(' => '', ')' => '', '*' => '', '+' => '', ',' => '', '/' => '', ';' => '',
        '<' => '', '=' => '', '>' => '', '?' => '', '@' => '', '[' => '', '\\' => '',
        ']' => '', '^' => '', '`' => '', '{' => '', '|' => '', '}' => '', '~' => '',
        ' ' => '_', ':' => '_'
    );

    /**
     * Characters for replacement in the tag value.
     *
     * @var array
     */
    protected $_tagValueReplacementCharMap = array(
        '&' => '&amp;' // replace "&" with HTML entity, because it is not replaced by default
    );

    /**
     * Protected pattern for check chars in the begin of tag name.
     *
     * @var string
     */
    // TODO: What a strange name? Why does 'protected' mean?
    protected $_protectedTagNamePattern = '/^[0-9,.-]/';

    /**
     * Convert Array to XML.
     *
     * @param mixed $data
     * @return string
     */
    public function render($data)
    {
        /** @var Mage_Webapi_Controller_Response_Renderer_Xml_Writer $writer */
        $writer = Mage::getModel('Mage_Webapi_Controller_Response_Renderer_Xml_Writer', array(
            'config' => new Zend_Config($this->_prepareData($data, true))
        ));
        return $writer->render();
    }

    /**
     * Prepare convert data.
     *
     * @param array|Varien_Object $data
     * @param bool $root
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _prepareData($data, $root = false)
    {
        if (!is_array($data) && !is_object($data)) {
            if ($root) {
                $data = array($data);
            } else {
                throw new InvalidArgumentException('Data must be an object or an array.');
            }
        }
        $data = $data instanceof Varien_Object ? $data->toArray() : (array)$data;
        $isAssoc = !preg_match('/^\d+$/', implode(array_keys($data), ''));

        $preparedData = array();
        foreach ($data as $key => $value) {
            $value = is_array($value) || is_object($value) ? $this->_prepareData($value) : $this->_prepareValue($value);
            if ($isAssoc) {
                $preparedData[$this->_prepareKey($key)] = $value;
            } else {
                $preparedData[self::DEFAULT_INDEXED_ARRAY_ITEM_NAME][] = $value;
            }
        }
        return $preparedData;
    }

    /**
     * Prepare value.
     *
     * @param string $value
     * @return string
     */
    protected function _prepareValue($value)
    {
        return str_replace(
            array_keys($this->_tagValueReplacementCharMap),
            array_values($this->_tagValueReplacementCharMap),
            $value
        );
    }

    /**
     * Prepare key and replace unavailable chars.
     *
     * @param string $key
     * @return string
     */
    protected function _prepareKey($key)
    {
        $key = str_replace(array_keys($this->_tagNameReplacementCharMap),
            array_values($this->_tagNameReplacementCharMap), $key);
        $key = trim($key, '_');
        if (preg_match($this->_protectedTagNamePattern, $key)) {
            $key = self::DEFAULT_INDEXED_ARRAY_ITEM_NAME . '_' . $key;
        }
        return $key;
    }

    /**
     * Get XML renderer MIME type.
     *
     * @return string
     */
    public function getMimeType()
    {
        return self::MIME_TYPE;
    }
}
