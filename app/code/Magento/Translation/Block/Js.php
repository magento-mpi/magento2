<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Translation\Block;

use Magento\Framework\Translate\InlineInterface as InlineTranslator;
use Magento\Translation\Model\Js as DataProvider;
use Magento\Framework\View\Element\Template;

class Js extends \Magento\Framework\View\Element\Template
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
     * @param Template\Context $context
     * @param DataProvider $dataProvider
     * @param InlineTranslator $translateInline
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        DataProvider $dataProvider,
        InlineTranslator $translateInline,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataProvider = $dataProvider;
        $this->translateInline = $translateInline;
    }

    /**
     * @return string
     */
    public function getTranslatedJson()
    {
        $json = \Zend_Json::encode($this->dataProvider->getTranslateData());
        $this->translateInline->processResponseBody($json, false);
        return $json;
    }
}
