<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Mage_Core_Model_Url_RewriteFactory implements Magento_ObjectManager_Factory
{
    /**
     * Entity class name
     */
    const CLASS_NAME = 'Mage_Core_Model_Url_Rewrite';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param array $arguments
     * @return Mage_Core_Model_Url_Rewrite
     */
    public function createFromArray(array $arguments = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $arguments, false);
    }
}
