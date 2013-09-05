<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Quick style renderer factory
 */
class Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Factory
{
    /**
     * Background image attribute
     */
    const BACKGROUND_IMAGE = 'background-image';

    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Specific renderer list
     *
     * @var array
     */
    protected $_specificRenderer = array(
        self::BACKGROUND_IMAGE => 'Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_BackgroundImage',
    );

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new instance
     *
     * @param string $attribute
     * @return Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Abstract
     */
    public function get($attribute)
    {
        $renderer = array_key_exists($attribute, $this->_specificRenderer)
            ? $this->_specificRenderer[$attribute]
            : 'Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Default';

        return $this->_objectManager->create($renderer);
    }
}
