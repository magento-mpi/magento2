<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration comment model factory
 */
namespace Magento\Backend\Model\Config;

class CommentFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new config object
     *
     * @param string $type
     * @return \Magento\Backend\Model\Config\CommentInterface
     * @throws \InvalidArgumentException
     */
    public function create($type)
    {
        $commentModel = $this->_objectManager->create($type);
        if (!$commentModel instanceof \Magento\Backend\Model\Config\CommentInterface) {
            throw new \InvalidArgumentException('Incorrect comment model provided');
        }
        return $commentModel;
    }
}
