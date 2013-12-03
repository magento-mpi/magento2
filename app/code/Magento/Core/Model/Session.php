<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

/**
 * Core session model
 *
 * @todo extend from \Magento\Core\Model\Session\AbstractSession
 *
 * @method null|bool getCookieShouldBeReceived()
 * @method \Magento\Core\Model\Session setCookieShouldBeReceived(bool $flag)
 * @method \Magento\Core\Model\Session unsCookieShouldBeReceived()
 */
class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @param \Magento\Core\Model\Session\Context $context
     * @param \Magento\Math\Random $mathRandom
     * @param mixed $sessionName
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        \Magento\Math\Random $mathRandom,
        $sessionName = null,
        array $data = array()
    ) {
        $this->mathRandom = $mathRandom;
        parent::__construct($context, $data);
        $this->init('core', $sessionName);
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string A 16 bit unique key for forms
     */
    public function getFormKey()
    {
        if (!$this->getData('_form_key')) {
            $this->setData('_form_key', $this->mathRandom->getRandomString(16));
        }
        return $this->getData('_form_key');
    }
}
