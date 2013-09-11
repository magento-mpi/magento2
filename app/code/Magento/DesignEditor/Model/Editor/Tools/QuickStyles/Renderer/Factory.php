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
namespace Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer;

class Factory
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
        self::BACKGROUND_IMAGE => '\Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\BackgroundImage',
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
     * @return \Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\AbstractRenderer
     */
    public function get($attribute)
    {
        $renderer = array_key_exists($attribute, $this->_specificRenderer)
            ? $this->_specificRenderer[$attribute]
            : '\Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\DefaultRenderer';

        return $this->_objectManager->create($renderer);
    }
}
