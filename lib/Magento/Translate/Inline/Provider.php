<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Translate\Inline;

class Provider implements ProviderInterface
{
    /**
     * @var \Magento\Translate\InlineInterface
     */
    protected $inlineTranslate;

    /**
     * @param \Magento\Translate\InlineInterface $inlineTranslate
     */
    public function __construct(\Magento\Translate\InlineInterface $inlineTranslate)
    {
        $this->inlineTranslate = $inlineTranslate;
    }

    /**
     * Return instance of inline translate class
     *
     * @return \Magento\Translate\InlineInterface
     */
    public function get()
    {
        return $this->inlineTranslate;
    }
}
