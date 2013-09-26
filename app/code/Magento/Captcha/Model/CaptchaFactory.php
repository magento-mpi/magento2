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
     * Get captcha instance
     *
     * @param string $captchaType
     * @param string $formId
     * @return Magento_Captcha_Model_Interface
     * @throws InvalidArgumentException
     */
    public function create($captchaType, $formId)
    {
        $className = 'Magento_Captcha_Model_' . ucfirst($captchaType);

        $instance = $this->_objectManager->create($className, array('formId' => $formId));
        if (!($instance instanceof Magento_Captcha_Model_Interface)) {
            throw new InvalidArgumentException($className . ' does not implement Magento_Captcha_Model_Interface');
        }
        return $instance;
    }
}