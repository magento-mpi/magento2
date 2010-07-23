<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Model_Theme
{
    protected $_file;
    protected $_xml;
    protected $_conf;

    public function __construct($file)
    {
        $this->_file = $file;
        $text = file_get_contents($file);
        $this->_xml = simplexml_load_string($text);
        if (empty($this->_xml)) {
            throw new Exception('Invalid XML');
        }
        $this->_conf = $this->_xmlToArray($this->_xml->configuration);
        $this->_conf = $this->_conf['configuration'];
        if (!is_array($this->_conf)) {
            throw new Exception('Wrong theme format');
        }
    }

    protected function _xmlToArray($xml)
    {
        $result = array();
        foreach ($xml as $key => $value) {
            if (count($value)) {
                $result[$key] = $this->_xmlToArray($value);
            } else {
                $result[$key] = (string) $value;
            }
        }
        return $result;
    }

    public function getName()
    {
        return (string) $this->_xml->manifest->name;
    }

    public function getLabel()
    {
        return (string) $this->_xml->manifest->label;
    }

    /**
     * Load data (flat array) for Varien_Data_Form
     *
     * @return array
     */
    public function getFormData()
    {
        return $this->_flatArray($this->_conf, 'conf');
    }

    /**
     * Load data (flat array) for Varien_Data_Form
     *
     * @param array $subtree
     * @param string $prefix
     * @return array
     */
    protected function _flatArray($subtree, $prefix=null)
    {
        $result = array();
        foreach ($subtree as $key => $value) {
            if (is_null($prefix)) {
                $name = $key;
            }
            else {
                $name = $prefix . '[' . $key . ']';
            }

            if (is_array($value)) {
                $result = array_merge($result, $this->_flatArray($value, $name));
            }
            else {
                $result[$name] = $value;
            }
        }
        return $result;
    }

    private function _validateFormInput($data, $xml=NULL) {
        $root = FALSE;
        $result = array();
        if (is_null($xml)) {
            $root = TRUE;
            $data = array('configuration' => $data);
            $xml = $this->_xml->configuration;
        }
        foreach ($xml as $key => $value) {
            if (isset($data[$key])) {
                if (is_array($data[$key])) {
                    $result[$key] = $this->_validateFormInput($data[$key], $value);
                }
                else {
                    $result[$key] = $data[$key];
                }
            }
        }
        if ($root) {
            $result = $result['configuration'];
        }
        return $result;
    }

    private function _buildRecursive($parent, $data)
    {
        foreach ($data as $key=>$value) {
            if (is_array($value)) {
                $this->_buildRecursive($parent->addChild($key), $value);
            }
            else {
                $parent->addChild($key, $value);
            }
        }
    }

    public function importAndSaveData($data)
    {
        $xml = new SimpleXMLElement('<theme>'.$this->_xml->manifest->asXML().'</theme>');
        $this->_buildRecursive($xml->addChild('configuration'), $this->_validateFormInput($data));
        clearstatcache();
        if (is_writeable($this->_file)) {
            file_put_contents($this->_file, $xml->asXML());
        } else {
            Mage::throwException(Mage::helper('xmlconnect')->__('Can\'t write to file "%s".', $this->_file));
        }
    }
}
