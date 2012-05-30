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
    /**
     * @var int
     */
    protected $_parentId;

    protected $_attributes = array(
        'translation_module' => 'module',
    );

    protected $_children = array(
        'title' => 'title'
    );

    /**
     * @param array $data
     */
    public function addData(array $data)
    {
        foreach ($data as $key => $value) {
            if ($key == 'parent' && !$this->_parent) {
                $this->_parent = $value;
                continue;
            }
            if (isset($this->_attributes[$key])) {
                if (is_null($this->getAttribute($key))) {
                    $this->addAttribute($key, $value);
                }
            }
            if (isset($this->_children[$key])) {
                if (is_null)
                $this->_addChild($key, $value);
            }
        }
    }

    /**
     * @param array $data
     */
    public function updateData(array $data)
    {
        foreach ($data as $key => $value) {
            if ($key == 'parent') {
                $this->_parent = $value;
                continue;
            }
            if (isset($this->_attributes[$key])) {
                $this->addAttribute($key, $value);
            }
            if (isset($this->_children[$key])) {
                if ($this->hasChildren() && $this->children($key)) {

                }
                $this->_addChild($key, $value);
            }
        }
    }

    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->_parentId;
    }
}
