<?php
/**
 *  XML Renderer allows to format array or object as valid XML document.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Response_Rest_Renderer_Xml implements
    Mage_Webapi_Controller_Response_Rest_RendererInterface
{
    /**
     * Renderer mime type.
     */
    const MIME_TYPE = 'application/xml';

    /**
     * Root node in XML output.
     */
    const XML_ROOT_NODE = 'response';

    /**
     * This value is used to replace numeric keys while formatting data for xml output.
     */
    const DEFAULT_ENTITY_ITEM_NAME = 'item';

    /** @var Mage_Xml_Generator */
    protected $_xmlGenerator;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Xml_Generator $xmlGenerator
     */
    public function __construct(Mage_Xml_Generator $xmlGenerator)
    {
        $this->_xmlGenerator = $xmlGenerator;
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

    /**
     * Format object|array to valid XML.
     *
     * @param array|Varien_Object $data
     * @return string
     */
    public function render($data)
    {
        $formattedData = $this->_formatData($data, true);
        /** Wrap response in a single node. */
        $formattedData = array(self::XML_ROOT_NODE => $formattedData);
        $this->_xmlGenerator->arrayToXml($formattedData);
        return $this->_xmlGenerator->getDom()->saveXML();
    }

    /**
     * Reformat mixed data to multidimensional array.
     *
     * This method is recursive.
     *
     * @param array|Varien_Object $data
     * @param bool $isRoot
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _formatData($data, $isRoot = false)
    {
        if (!is_array($data) && !is_object($data)) {
            if ($isRoot) {
                $data = array($data);
            } else {
                throw new InvalidArgumentException('Data must be an object or an array.');
            }
        }
        $data = $data instanceof Varien_Object ? $data->toArray() : (array)$data;
        $isAssoc = !preg_match('/^\d+$/', implode(array_keys($data), ''));

        $formattedData = array();
        foreach ($data as $key => $value) {
            $value = is_array($value) || is_object($value) ? $this->_formatData($value) : $this->_formatValue($value);
            if ($isAssoc) {
                $formattedData[$this->_prepareKey($key)] = $value;
            } else {
                $formattedData[self::DEFAULT_ENTITY_ITEM_NAME][] = $value;
            }
        }
        return $formattedData;
    }

    /**
     * Prepare value in contrast with key.
     *
     * @param string $value
     * @return string
     */
    protected function _formatValue($value)
    {
        $replacementMap = array('&' => '&amp;');
        return str_replace(array_keys($replacementMap), array_values($replacementMap), $value);
    }

    /**
     * Format array key or field name to be valid array key name.
     *
     * Replaces characters that are invalid in array key names.
     *
     * @param string $key
     * @return string
     */
    protected function _prepareKey($key)
    {
        $replacementMap = array(
            '!' => '', '"' => '', '#' => '', '$' => '', '%' => '', '&' => '', '\'' => '',
            '(' => '', ')' => '', '*' => '', '+' => '', ',' => '', '/' => '', ';' => '',
            '<' => '', '=' => '', '>' => '', '?' => '', '@' => '', '[' => '', '\\' => '',
            ']' => '', '^' => '', '`' => '', '{' => '', '|' => '', '}' => '', '~' => '',
            ' ' => '_', ':' => '_'
        );
        $key = str_replace(array_keys($replacementMap), array_values($replacementMap), $key);
        $key = trim($key, '_');
        $prohibitedTagNamePattern = '/^[0-9,.-]/';
        if (preg_match($prohibitedTagNamePattern, $key)) {
            $key = self::DEFAULT_ENTITY_ITEM_NAME . '_' . $key;
        }
        return $key;
    }
}
