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
 * Inline Translation config factory
 */
namespace Magento\Core\Model\Translate\Inline;

class ConfigFactory
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
     * Create instance of inline translate config
     *
     * @return \Magento\Core\Model\Translate\Inline\ConfigInterface
     */
    public function create()
    {
        return $this->_objectManager->create('Magento\Core\Model\Translate\Inline\Config');
    }
}
