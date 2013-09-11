<?php
/**
 * Captcha model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Captcha\Model;

class CaptchaFactory
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
     * Get captcha instance
     *
     * @param string $instanceName
     * @param array $params
     * @return \Magento\Captcha\Model\ModelInterface
     * @throws \InvalidArgumentException
     */
    public function create($instanceName, array $params = array())
    {
        $instance = $this->_objectManager->create($instanceName, $params);
        if (!($instance instanceof \Magento\Captcha\Model\ModelInterface)) {
            throw new \InvalidArgumentException($instanceName . ' does not implements \Magento\Captcha\Model\ModelInterface');
        }
        return $instance;
    }
}
