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
class Magento_Core_Model_Translate_Factory
{
    /**
     * Default translate inline class name
     */
    const DEFAULT_CLASS_NAME = 'Magento_Core_Model_Translate_Inline';

    /**
     * Object Manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Object constructor
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return instance of inline translate object based on passed in class name.
     *
     * @param array $data
     * @param string $className
     * @return Magento_Core_Model_Translate_InlineInterface
     */
    public function create(array $data = null, $className = null)
    {
        if ($className === null) {
            $className = self::DEFAULT_CLASS_NAME;
        }
        return $this->_objectManager->get($className, $data);
    }
}
