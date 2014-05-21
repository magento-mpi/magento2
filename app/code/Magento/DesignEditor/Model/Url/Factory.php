<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model\Url;

class Factory
{
    /**
     * Default url model class name
     */
    const CLASS_NAME = 'Magento\Framework\UrlInterface';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Replace name of url model
     *
     * @param string $className
     * @return $this
     */
    public function replaceClassName($className)
    {
        $this->_objectManager->configure(array('preferences' => array(self::CLASS_NAME => $className)));

        return $this;
    }

    /**
     * Create url model new instance
     *
     * @param array $arguments
     * @return \Magento\Framework\UrlInterface
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $arguments);
    }
}
