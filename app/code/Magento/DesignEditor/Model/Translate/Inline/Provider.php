<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\DesignEditor\Model\Translate\Inline;

class Provider extends \Magento\Translate\Inline\Provider
{
    /**
     * @var \Magento\Translate\InlineInterface
     */
    protected $vdeInlineTranslate;

    /**
     * @var \Magento\Translate\InlineInterface
     */
    protected $inlineTranslate;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\DesignEditor\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Translate\InlineInterface $vdeInlineTranslate
     * @param \Magento\Translate\InlineInterface $inlineTranslate
     * @param \Magento\DesignEditor\Helper\Data $helper
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Translate\InlineInterface $vdeInlineTranslate,
        \Magento\Translate\InlineInterface $inlineTranslate,
        \Magento\DesignEditor\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->vdeInlineTranslate = $vdeInlineTranslate;
        $this->inlineTranslate = $inlineTranslate;
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * Return instance of inline translate class
     *
     * @return \Magento\Translate\InlineInterface
     */
    public function get()
    {
        return $this->helper->isVdeRequest($this->request)
            ? $this->vdeInlineTranslate
            : $this->inlineTranslate;
    }
}
