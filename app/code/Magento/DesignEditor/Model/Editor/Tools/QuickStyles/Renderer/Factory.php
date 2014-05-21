<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer;

/**
 * Quick style renderer factory
 */
class Factory
{
    /**
     * Background image attribute
     */
    const BACKGROUND_IMAGE = 'background-image';

    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Specific renderer list
     *
     * @var array
     */
    protected $_specificRenderer = array(
        self::BACKGROUND_IMAGE => 'Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\BackgroundImage'
    );

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
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
        $renderer = array_key_exists(
            $attribute,
            $this->_specificRenderer
        ) ? $this->_specificRenderer[$attribute] : 'Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\DefaultRenderer';

        return $this->_objectManager->create($renderer);
    }
}
