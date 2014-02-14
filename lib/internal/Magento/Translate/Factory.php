<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Translate factory
 */
namespace Magento\Translate;

class Factory
{
    /**
     * Default translate inline class name
     */
    const DEFAULT_CLASS_NAME = 'Magento\Translate\InlineInterface';

    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Object constructor
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return instance of inline translate object based on passed in class name.
     *
     * @param array $data
     * @param string $className
     * @throws \InvalidArgumentException
     * @return \Magento\Translate\InlineInterface
     */
    public function create(array $data = null, $className = null)
    {
        if ($className === null) {
            $className = self::DEFAULT_CLASS_NAME;
        }
        $model = $this->_objectManager->get($className, $data);
        if (!$model instanceof \Magento\Translate\InlineInterface) {
            throw new \InvalidArgumentException('Invalid inline translate model: ' . $className);
        }
        return $model;
    }
}
