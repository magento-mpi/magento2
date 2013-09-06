<?php
/**
 * Captcha model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Captcha_Model_CaptchaFactory
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
     * @return Magento_Captcha_Model_Interface
     * @throws InvalidArgumentException
     */
    public function create($instanceName, array $params = array())
    {
        $instance = $this->_objectManager->create($instanceName, $params);
        if (!($instance instanceof Magento_Captcha_Model_Interface)) {
            throw new InvalidArgumentException($instanceName . ' does not implements Magento_Captcha_Model_Interface');
        }
        return $instance;
    }
}