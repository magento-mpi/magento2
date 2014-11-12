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
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get captcha instance
     *
     * @param string $captchaType
     * @param string $formId
     * @return \Magento\Captcha\Model\ModelInterface
     * @throws \InvalidArgumentException
     */
    public function create($captchaType, $formId)
    {
        $className = 'Magento\Captcha\Model\\' . ucfirst($captchaType);

        $instance = $this->_objectManager->create($className, array('formId' => $formId));
        if (!$instance instanceof \Magento\Captcha\Model\ModelInterface) {
            throw new \InvalidArgumentException(
                $className . ' does not implement \Magento\Captcha\Model\ModelInterface'
            );
        }
        return $instance;
    }
}
