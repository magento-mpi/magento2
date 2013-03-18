<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Translate factory
 */
class Mage_Core_Model_Translate_Factory
{
    /**
     * Default translate inline class name
     */
    const DEFAULT_CLASS_NAME = 'Mage_Core_Model_Translate_Inline';

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
     * Return instance of Translate object based on passed in class name.
     *
     * @param $translateConfig Mage_Core_Model_Translate_Config
     * @return Mage_Core_Model_Translate_TranslateInterface
     */
    public function createFromConfig($translateConfig)
    {
        $className = $translateConfig->getInlineType();
        if ($className === null) {
            $className = self::DEFAULT_CLASS_NAME;
        }
        return $this->_objectManager->get($className, $translateConfig->getParams());
    }
}
