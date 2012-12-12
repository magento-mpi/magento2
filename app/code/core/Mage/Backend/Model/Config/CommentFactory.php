<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration comment model factory
 */
class Mage_Backend_Model_Config_CommentFactory
{
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
     * Create new config object
     *
     * @param string $type
     * @return Mage_Backend_Model_Config_CommentInterface
     * @throws InvalidArgumentException
     */
    public function create($type)
    {
        $commentModel = $this->_objectManager->create($type);
        if (!$commentModel instanceof Mage_Backend_Model_Config_CommentInterface) {
            throw new InvalidArgumentException('Incorrect comment model provided');
        }
        return $commentModel;
    }
}
