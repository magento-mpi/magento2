<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Item extends Varien_Simplexml_Element
{
    protected $_children = array(
        'title' => 'title'
    );

    protected function _getAttributeMap()
    {
        return array(
            'id' => 'id',
            'parent' => 'parent',
            'module' => 'module',
        );
    }
    /**
     * @param array $data
     */
    public function addData(array $data)
    {
        $attributeMap = $this->_getAttributeMap();
        foreach ($data as $key => $value) {
            if (isset($attributeMap[$key])) {
                if (is_null($this->getAttribute($attributeMap[$key]))) {
                    $this->addAttribute($attributeMap[$key], $value);
                }
            }
            if (isset($this->_children[$key])) {
                //$this->_addChild($key, $value);
            }
        }
    }

    /**
     * @param array $data
     */
    public function updateData(array $data)
    {
        $attributeMap = $this->_getAttributeMap();
        foreach ($data as $key => $value) {
            if (isset($attributeMap[$key])) {
                $this->addAttribute($attributeMap[$key], $value);
            }
            if (isset($this->_children[$key])) {
                if ($this->hasChildren() && $this->children($key)) {

                }
                $this->_addChild($key, $value);
            }
        }
    }
}
