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
class Mage_DesignEditor_Model_Layout extends Mage_Core_Model_Layout
{
    /**
     * Flag that keeps true in case when we need to sanitize layout blocks
     *
     * @var bool
     */
    protected $_isSanitizeBlocks = false;

    /**
     * Is block wrapping enabled flag
     *
     * @var bool
     */
    protected $_enabledWrapping = false;

    /**
     * List of block types considered as "safe"
     *
     * "Safe" means that they will work with any template (if applicable)
     *
     * @var array
     */
    protected static $_blockWhiteList = array(
        'Mage_Core_Block_Template',
        'Mage_Page_Block_',
        'Mage_DesignEditor_Block_',
        'Mage_Checkout_Block_Onepage_',
        'Mage_Customer_Block_Account_Navigation',
        'Mage_Paypal_Block_Express_Review_Details',
        'Mage_Poll_Block_ActivePoll',
        'Mage_Sales_Block_Guest_Links',
        'Mage_Catalog_Block_Product_Compare_Sidebar',
        'Mage_Checkout_Block_Cart_Sidebar',
        'Mage_Wishlist_Block_Customer_Sidebar',
        'Mage_Reports_Block_Product_Viewed',
        'Mage_Reports_Block_Product_Compared',
        'Mage_Sales_Block_Reorder_Sidebar',
        'Mage_Paypal_Block_Express_Shortcut',
        'Mage_PaypalUk_Block_Express_Shortcut',
    );

    /**
     * List of block types considered as "not safe"
     *
     * @var array
     */
    protected static $_blockBlackList = array(
        'Mage_Page_Block_Html_Pager',
    );

    /**
     * List of layout containers that potentially have "safe" blocks
     *
     * @var array
     */
    protected static $_containerWhiteList = array(
        'root', 'head', 'after_body_start', 'header', 'footer', 'before_body_end',
        'top.links', 'top.menu',
    );

    /**
     * Block that wrap page elements when wrapping enabled
     *
     * @var Mage_DesignEditor_Block_Template
     */
    protected $_wrapperBlock;

    /**
     * Class constructor
     *
     * @param Mage_Core_Model_BlockFactory $blockFactory
     * @param Magento_Data_Structure $structure
     * @param Mage_Core_Model_Layout_Argument_Processor $argumentProcessor
     * @param Mage_Core_Model_Layout_Translator $translator
     * @param Mage_Core_Model_Layout_ScheduledStructure $scheduledStructure
     * @param Mage_DesignEditor_Block_Template $wrapperBlock
     * @param string $area
     * @param bool $isSanitizeBlocks
     * @param bool $enableWrapping
     */
    public function __construct(
        Mage_Core_Model_BlockFactory $blockFactory,
        Magento_Data_Structure $structure,
        Mage_Core_Model_Layout_Argument_Processor $argumentProcessor,
        Mage_Core_Model_Layout_Translator $translator,
        Mage_Core_Model_Layout_ScheduledStructure $scheduledStructure,
        Mage_DesignEditor_Block_Template $wrapperBlock,
        $area = Mage_Core_Model_Design_Package::DEFAULT_AREA,
        $isSanitizeBlocks = false,
        $enableWrapping = false
    ) {
        $this->_wrapperBlock     = $wrapperBlock;
        $this->_isSanitizeBlocks = $isSanitizeBlocks;
        $this->_enabledWrapping  = $enableWrapping;
        parent::__construct($blockFactory, $structure, $argumentProcessor, $translator, $scheduledStructure, $area);
    }

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
        if (self::_isParentSafe($node) || self::_isTypeSafe($type)) {
            return;
        }
        self::_overrideAttribute($node, 'template', 'Mage_DesignEditor::stub.phtml');
        self::_overrideAttribute($node, 'type', 'Mage_Core_Block_Template');
        self::_deleteNodes($node, 'action');
    }

    /**
     * Whether parent node of specified node can be considered a safe container
     *
     * @param Varien_Simplexml_Element $node
     * @return bool
     */
    protected static function _isParentSafe(Varien_Simplexml_Element $node)
    {
        $parentAttributes = $node->getParent()->attributes();
        if (isset($parentAttributes['name'])) {
            if (!in_array($parentAttributes['name'], self::$_containerWhiteList)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check whether the specified type of block can be safely used in layout without required context
     *
     * @param string $type
     * @return bool
     */
    protected static function _isTypeSafe($type)
    {
        if (in_array($type, self::$_blockBlackList)) {
            return false;
        }
        foreach (self::$_blockWhiteList as $safeType) {
            if ('_' !== substr($safeType, -1, 1)) {
                if ($type === $safeType) {
                    return true;
                }
            } elseif (0 === strpos($type, $safeType)) {
                return true;
            }
        }
        return false;
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
            $isTypeSafe = self::_isTypeSafe($block->getAttribute('type'));
            if (!$isTypeSafe || !self::_isParentSafe($block)) {
                self::_deleteNodes($node, 'action');
            }
            break;
        }
    }

    /**
     * Create structure of elements from the loaded XML configuration
     */
    public function generateElements()
    {
        if ($this->_isSanitizeBlocks) {
            $this->sanitizeLayout($this->getNode());
        }

        parent::generateElements();
    }

    /**
     * Gets HTML of block element
     *
     * @param string $name
     * @return string
     * @throws Magento_Exception
     */
    protected function _renderBlock($name)
    {
        $result = parent::_renderBlock($name);

        if ($this->_enabledWrapping) {
            $block = $this->getBlock($name);
            $isManipulationAllowed = $this->isManipulationAllowed($name)
                && strpos(get_class($block), 'Mage_DesignEditor_Block_') === 0;

            $result = $this->_wrapElement($result, $name, false, $isManipulationAllowed);
        }

        return $result;
    }

    /**
     * Gets HTML of container element
     *
     * @param string $name
     * @return string
     */
    protected function _renderContainer($name)
    {
        $result = parent::_renderContainer($name);

        if ($this->_enabledWrapping) {
            $result = $this->_wrapElement($result, $name, true);
        }

        return $result;
    }

    /**
     * Wrap layout element
     *
     * @param string $elementContent
     * @param string $elementName
     * @param bool $isContainer
     * @param bool $isManipulationAllowed
     * @return string
     */
    protected function _wrapElement($elementContent, $elementName, $isContainer = false, $isManipulationAllowed = false)
    {
        $elementId = 'vde_element_' . rtrim(strtr(base64_encode($elementName), '+/', '-_'), '=');
        $this->_wrapperBlock->setData(array(
            'element_id'              => $elementId,
            'element_title'           => $this->getElementProperty($elementName, 'label') ?: $elementName,
            'element_html'            => $elementContent,
            'is_manipulation_allowed' => $isManipulationAllowed,
            'is_container'            => $isContainer,
            'element_name'            => $elementName,
        ));
        return $this->_wrapperBlock->toHtml();
    }
}
