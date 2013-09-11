<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Translate factory
 */
namespace Magento\Core\Model\Translate;

class Factory
{
    /**
     * Default translate inline class name
     */
    const DEFAULT_CLASS_NAME = '\Magento\Core\Model\Translate\Inline';

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
     * @return \Magento\Core\Model\Translate\InlineInterface
     */
    public function create(array $data = null, $className = null)
    {
        if ($className === null) {
            $className = self::DEFAULT_CLASS_NAME;
        }
        return $this->_objectManager->get($className, $data);
    }
}
