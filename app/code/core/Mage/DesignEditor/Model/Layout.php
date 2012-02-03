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
 * Model for manipulating layout for purpose of design editor
 */
class Mage_DesignEditor_Model_Layout
{
    /**
     * List of block types considered as "safe"
     *
     * "Safe" means that they will work with any template (if applicable)
     *
     * @var array
     */
    protected static $_safeBlockTypes = array(
        'Mage_Core_Block_Template',
        'Mage_Core_Block_Text_List',
        'Mage_Page_Block_Html',
        'Mage_Page_Block_Html_Head',
        'Mage_Page_Block_Html_Wrapper'
    );

    /**
     * Replace all potentially dangerous blocks in layout into stubs
     *
     * It is important to sanitize the references first, because they refer to blocks to check whether they are safe.
     * But if the blocks were sanitized before references, then they ALL will be considered safe.
     *
     * @param Varien_Simplexml_Element $node
     */
    public static function sanitizeLayout(Varien_Simplexml_Element $node)
    {
        self::_sanitizeLayout($node, 'reference'); // it is important to sanitize references first
        self::_sanitizeLayout($node, 'block');
    }

    /**
     * Sanitize nodes which names match the specified one
     *
     * Recursively goes through all underlying nodes
     *
     * @param Varien_Simplexml_Element $node
     * @param string $nodeName
     */
    protected static function _sanitizeLayout(Varien_Simplexml_Element $node, $nodeName)
    {
        if ($node->getName() == $nodeName) {
            switch ($nodeName) {
                case 'block':
                    self::_sanitizeBlock($node);
                    break;
                case 'reference':
                    self::_sanitizeReference($node);
                    break;
            }
        }
        foreach ($node->children() as $child) {
            self::_sanitizeLayout($child, $nodeName);
        }
    }

    /**
     * Replace "unsafe" types of blocks into Mage_Core_Block_Template and cut all their actions
     *
     * A "stub" template will be assigned for the blocks
     *
     * @param Varien_Simplexml_Element $node
     */
    protected static function _sanitizeBlock(Varien_Simplexml_Element $node)
    {
        $type = $node->getAttribute('type');
        if (!$type) {
            return; // we encountered a node with name "block", however it doesn't actually define any block...
        }
        if (self::_isTypeSafe($type)) {
            return;
        }
        self::_overrideAttribute($node, 'template', 'Mage_DesignEditor::stub.phtml');
        self::_overrideAttribute($node, 'type', 'Mage_Core_Block_Template');
        self::_deleteNodes($node, 'action');
    }

    /**
     * Check whether the specified type of block can be safely used in layout without required context
     *
     * @param string $type
     * @return bool
     */
    protected static function _isTypeSafe($type)
    {
        return (0 === strpos($type, 'Mage_DesignEditor_Block_')) || in_array($type, self::$_safeBlockTypes);
    }

    /**
     * Add or update specified attribute of a node with specified value
     *
     * @param Varien_Simplexml_Element $node
     * @param string $name
     * @param string $value
     */
    protected static function _overrideAttribute(Varien_Simplexml_Element $node, $name, $value)
    {
        $attributes = $node->attributes();
        if (isset($attributes[$name])) {
            $attributes[$name] = $value;
        } else {
            $attributes->addAttribute($name, $value);
        }
    }

    /**
     * Delete child nodes by specified name
     *
     * @param Varien_Simplexml_Element $node
     * @param string $name
     */
    protected static function _deleteNodes(Varien_Simplexml_Element $node, $name)
    {
        $count = count($node->{$name});
        for ($i = $count; $i >= 0; $i--) {
            unset($node->{$name}[$i]);
        }
    }

    /**
     * Cleanup reference node according to the block it refers to
     *
     * Look for the block by reference name and if the block is "unsafe", cleanup the reference node from actions
     *
     * @param Varien_Simplexml_Element $node
     */
    protected static function _sanitizeReference(Varien_Simplexml_Element $node)
    {
        $attributes = $node->attributes();
        $name = $attributes['name'];
        $result = $node->xpath("//block[@name='{$name}']") ?: array();
        foreach ($result as $block) {
            if (!self::_isTypeSafe($block->getAttribute('type'))) {
                self::_deleteNodes($node, 'action');
            }
            break;
        }
    }
}
