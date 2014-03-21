<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\DesignEditor\Model\Translate\Inline;

use Magento\App\RequestInterface;

class Provider extends \Magento\Translate\Inline\Provider
{
    /**
     * XML path to VDE front name setting
     *
     * @var string
     */
    protected $frontName;

    /**
     * @var \Magento\Translate\InlineInterface
     */
    protected $vdeInlineTranslate;

    /**
     * @var \Magento\Translate\InlineInterface
     */
    protected $inlineTranslate;

    /**
     * @param \Magento\Translate\InlineInterface $vdeInlineTranslate
     * @param \Magento\Translate\InlineInterface $inlineTranslate
     * @param string $frontName
     */
    public function __construct(
        \Magento\Translate\InlineInterface $vdeInlineTranslate,
        \Magento\Translate\InlineInterface $inlineTranslate,
        $frontName
    ) {
        $this->vdeInlineTranslate = $vdeInlineTranslate;
        $this->inlineTranslate = $inlineTranslate;
        $this->frontName = $frontName;
    }

    /**
     * Return instance of inline translate class
     *
     * @return \Magento\Translate\InlineInterface
     */
    public function get()
    {
        return $this->isVdeRequest()
            ? $this->vdeInlineTranslate
            : $this->inlineTranslate;
    }

    /**
     * This method returns an indicator of whether or not the current request is for vde
     *
     * @param RequestInterface $request
     * @return bool
     */
    protected function isVdeRequest(RequestInterface $request = null)
    {
        $result = false;
        if (null !== $request) {
            $splitPath = explode('/', trim($request->getOriginalPathInfo(), '/'));
            if (count($splitPath) >= 3) {
                list($frontName, $currentMode, $themeId) = $splitPath;
                $result = ($frontName === $this->frontName)
                    && in_array($currentMode, [\Magento\DesignEditor\Model\State::MODE_NAVIGATION])
                    && is_numeric($themeId);
            }
        }
        return $result;
    }
}
