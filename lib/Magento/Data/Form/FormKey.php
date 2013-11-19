<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Data\Form;

class FormKey
{
    /**
     * Form key
     */
    const FORM_KEY = '_form_key';

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Magento\Core\Model\Session\AbstractSession
     */
    protected $session;

    /**
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Core\Model\Session\AbstractSession $session
     */
    public function __construct(
        \Magento\Math\Random $mathRandom,
        \Magento\Core\Model\Session\AbstractSession $session
    ) {
        $this->mathRandom = $mathRandom;
        $this->session = $session;
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string A 16 bit unique key for forms
     */
    public function getFormKey()
    {
        if (!$this->session->getData(self::FORM_KEY)) {
            $this->session->setData(self::FORM_KEY, $this->session->mathRandom->getRandomString(16));
        }
        return $this->session->getData(self::FORM_KEY);
    }
}
