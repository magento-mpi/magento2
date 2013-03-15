<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout remove change model
 */
class Mage_DesignEditor_Model_Change_Layout_Remove extends Mage_DesignEditor_Model_Change_LayoutAbstract
{
    /**
     * Layout directive associated with this change
     */
    const LAYOUT_DIRECTIVE_REMOVE = 'remove';

    /**
     * Class constructor
     *
     * @param Varien_Simplexml_Element|array $data
     */
    public function __construct($data = array())
    {
        if ($data instanceof Varien_Simplexml_Element) {
            $data = $this->_getAttributes($data);
        }

        parent::__construct($data);
    }

    /**
     * Get data to render layout update directive
     *
     * @return array
     */
    public function getLayoutUpdateData()
    {
        return array('name' => $this->getData('element_name'));
    }

    /**
     * Get layout update directive for given layout change
     *
     * @return string
     */
    public function getLayoutDirective()
    {
        return self::LAYOUT_DIRECTIVE_REMOVE;
    }

    /**
     * Get attributes from XML layout update
     *
     * @param Varien_Simplexml_Element $layoutUpdate
     * @return array
     */
    protected function _getAttributes(Varien_Simplexml_Element $layoutUpdate)
    {
        $attributes = array();
        if ($layoutUpdate->getAttribute('name') !== null) {
            $attributes['element_name'] = $layoutUpdate->getAttribute('name');
        }
        $attributes = array_merge($attributes, parent::_getAttributes($layoutUpdate));

        return $attributes;
    }
}
