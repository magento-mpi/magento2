<?php
namespace Magento\Code\Generator\TestAsset;

/**
 * Proxy class for Magento\Code\Generator\TestAsset\SourceClassWithNamespace
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class SourceClassWithNamespaceProxy extends \Magento\Code\Generator\TestAsset\SourceClassWithNamespace
{
    /**
     * Entity class name
     */
    const CLASS_NAME = 'Magento\Code\Generator\TestAsset\SourceClassWithNamespace';

    /**
     * Object Manager instance
     *
     * @var \Magento_ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Proxied instance
     *
     * @var Magento\Code\Generator\TestAsset\SourceClassWithNamespace
     */
    protected $_subject = null;

    /**
     * Proxy constructor
     *
     * @param \Magento_ObjectManager $objectManager
     */
    public function __construct(\Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array('_subject');
    }

    /**
     * Retrieve ObjectManager from global scope
     */
    public function __wakeup()
    {
        $this->_objectManager = Mage::getObjectManager();
    }

    /**
     * Clone proxied instance
     */
    public function __clone()
    {
        $this->_subject = clone $this->_objectManager->get(self::CLASS_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function publicChildMethod(\Zend\Code\Generator\ClassGenerator $classGenerator, $param1 = '', $param2 = '\\', $param3 = '\'', array $array = array())
    {
        if (!$this->_subject) {
            $this->_subject = $this->_objectManager->get(self::CLASS_NAME);
        }
        return $this->_subject->publicChildMethod($classGenerator, $param1, $param2, $param3, $array);
    }

    /**
     * {@inheritdoc}
     */
    public function publicMethodWithReference(\Zend\Code\Generator\ClassGenerator &$classGenerator, &$param1, array &$array)
    {
        if (!$this->_subject) {
            $this->_subject = $this->_objectManager->get(self::CLASS_NAME);
        }
        return $this->_subject->publicMethodWithReference($classGenerator, $param1, $array);
    }

    /**
     * {@inheritdoc}
     */
    public function publicChildWithoutParameters()
    {
        if (!$this->_subject) {
            $this->_subject = $this->_objectManager->get(self::CLASS_NAME);
        }
        return $this->_subject->publicChildWithoutParameters();
    }

    /**
     * {@inheritdoc}
     */
    public function publicParentMethod(\Zend\Code\Generator\DocBlockGenerator $docBlockGenerator, $param1 = '', $param2 = '\\', $param3 = '\'', array $array = array())
    {
        if (!$this->_subject) {
            $this->_subject = $this->_objectManager->get(self::CLASS_NAME);
        }
        return $this->_subject->publicParentMethod($docBlockGenerator, $param1, $param2, $param3, $array);
    }

    /**
     * {@inheritdoc}
     */
    public function publicParentWithoutParameters()
    {
        if (!$this->_subject) {
            $this->_subject = $this->_objectManager->get(self::CLASS_NAME);
        }
        return $this->_subject->publicParentWithoutParameters();
    }
}