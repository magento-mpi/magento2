<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Plugin;

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
