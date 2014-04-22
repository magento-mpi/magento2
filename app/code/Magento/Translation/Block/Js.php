<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Translation\Block;

use Magento\Framework\View\Element\BlockInterface;
use \Magento\Translation\Model\Js as DataProvider;
use \Magento\Translate\InlineInterface as InlineTranslator;

class Js
{
    /**
     * Data provider model
     *
     * @var DataProvider
     */
    protected $dataProvider;

    /**
     * Inline translator
     *
     * @var InlineTranslator
     */
    protected $translateInline;

    /**
     * @param DataProvider $dataProvider
     * @param InlineTranslator $translateInline
     */
    public function __construct(
        DataProvider $dataProvider,
        InlineTranslator $translateInline
    ) {
        $this->dataProvider = $dataProvider;
        $this->translateInline = $translateInline;
    }

    /**
     * Render js translation
     *
     * @return string
     */
    public function render()
    {
        $json = \Zend_Json::encode($this->dataProvider->getTranslateData());
        $this->translateInline->processResponseBody($json, false);
        $script = '(function($) {$.mage.translate.add(' . $json . ')})(jQuery);';
        return '<script type="text/javascript">//<![CDATA[' . "\n{$script}\n" . '//]]></script>';
    }
}
