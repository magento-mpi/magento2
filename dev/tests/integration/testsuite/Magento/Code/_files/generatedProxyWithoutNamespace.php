<?php
/**
 * Proxy class for Magento_Code_Generator_TestAsset_SourceClassWithoutNamespace
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Code_Generator_TestAsset_SourceClassWithoutNamespaceProxy extends \Magento_Code_Generator_TestAsset_SourceClassWithoutNamespace
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Proxied instance
     *
     * @var \Magento_Code_Generator_TestAsset_SourceClassWithoutNamespace
     */
    protected $_subject = null;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $_isShared = null;

    /**
     * Proxy constructor
     *
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(\Magento\ObjectManager $objectManager, $instanceName = 'Magento_Code_Generator_TestAsset_SourceClassWithoutNamespace', $shared = true)
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array('_subject', '_isShared');
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
        $this->_subject = clone $this->_getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \Magento_Code_Generator_TestAsset_SourceClassWithoutNamespace
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = true === $this->_isShared
                ? $this->_objectManager->get($this->_instanceName)
                : $this->_objectManager->create($this->_instanceName);
        }
        return $this->_subject;
    }

    /**
     * {@inheritdoc}
     */
    public function publicChildMethod(\Zend\Code\Generator\ClassGenerator $classGenerator, $param1 = '', $param2 = '\\', $param3 = '\'', array $array = array())
    {
        return $this->_getSubject()->publicChildMethod($classGenerator, $param1, $param2, $param3, $array);
    }

    /**
     * {@inheritdoc}
     */
    public function publicMethodWithReference(\Zend\Code\Generator\ClassGenerator &$classGenerator, &$param1, array &$array)
    {
        return $this->_getSubject()->publicMethodWithReference($classGenerator, $param1, $array);
    }

    /**
     * {@inheritdoc}
     */
    public function publicChildWithoutParameters()
    {
        return $this->_getSubject()->publicChildWithoutParameters();
    }

    /**
     * {@inheritdoc}
     */
    public function publicParentMethod(\Zend\Code\Generator\DocBlockGenerator $docBlockGenerator, $param1 = '', $param2 = '\\', $param3 = '\'', array $array = array())
    {
        return $this->_getSubject()->publicParentMethod($docBlockGenerator, $param1, $param2, $param3, $array);
    }

    /**
     * {@inheritdoc}
     */
    public function publicParentWithoutParameters()
    {
        return $this->_getSubject()->publicParentWithoutParameters();
    }
}
