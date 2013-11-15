<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translate\Plugin;

class Translate
{
    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $translator;

    /**
     * @param \Magento\Core\Model\Translate $translator
     */
    public function __construct(
        \Magento\Core\Model\Translate $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return string
     */
    public function aroundJsonEncode(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $returnValue = $invocationChain->proceed($arguments);
        if ($this->translator->isAllowed()) {
            $this->translator->processResponseBody($returnValue, true);
        }
        return $returnValue;
    }

    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     */
    public function aroundSendPaymentFailedEmail(
        array $arguments,
        \Magento\Code\Plugin\InvocationChain $invocationChain
    ) {
        $this->translator->setTranslateInline(false);
        $invocationChain->proceed($arguments);
        $this->translator->setTranslateInline(true);
    }
}
