<?php
/**
 * Inline Translations Library
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translate;

class Inline implements \Magento\Translate\InlineInterface
{
    /**
     * Regular Expression for detected and replace translate
     *
     * @var string
     */
    protected $_tokenRegex = '\{\{\{(.*?)\}\}\{\{(.*?)\}\}\{\{(.*?)\}\}\{\{(.*?)\}\}\}';

    /**
     * @var \Magento\TranslateInterface
     */
    protected $_translator;
    /**
     * Indicator to hold state of whether inline translation is allowed
     *
     * @var bool
     */
    protected $_isAllowed;

    /**
     * @var \Magento\Translate\Inline\ParserInterface
     */
    protected $_parser;

    /**
     * Flag about inserted styles and scripts for inline translates
     *
     * @var bool
     */
    protected $_isScriptInserted    = false;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Translate\Inline\ConfigFactory
     */
    protected $_configFactory;

    /**
     * @var \Magento\BaseScopeResolverInterface
     */
    protected $_scopeResolver;

    /**
     * Initialize inline translation model
     *
     * @param \Magento\BaseScopeResolverInterface $scopeResolver
     * @param \Magento\Translate\Inline\ParserInterface $parser
     * @param \Magento\TranslateInterface $translate
     * @param \Magento\UrlInterface $url
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Translate\Inline\ConfigFactory $configFactory
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\BaseScopeResolverInterface $scopeResolver,
        \Magento\Translate\Inline\ParserInterface $parser,
        \Magento\TranslateInterface $translate,
        \Magento\UrlInterface $url,
        \Magento\View\LayoutInterface $layout,
        \Magento\Translate\Inline\ConfigFactory $configFactory,
        \Magento\App\State $appState
    ) {
        $this->_scopeResolver = $scopeResolver;
        $this->_configFactory = $configFactory;
        $this->_parser = $parser;
        $this->_translator = $translate;
        $this->_url = $url;
        $this->_layout = $layout;
        $this->_appState = $appState;
    }

    /**
     * Is enabled and allowed Inline Translates
     *
     * @param mixed $scope
     * @return bool
     */
    public function isAllowed($scope = null)
    {
        if (is_null($this->_isAllowed)) {
            if (!$scope instanceof \Magento\App\Config\ScopeInterface) {
                $scope = $this->_scopeResolver->getScope($scope);
            }

            $config = $this->_configFactory->create();
            $this->_isAllowed = $config->isActive($scope) && $config->isDevAllowed($scope);
        }
        return $this->_translator->getTranslateInline() && $this->_isAllowed;
    }

    /**
     * Disable inline translation functionality
     */
    public function disable()
    {
        $this->_isAllowed = false;
    }

    /**
     * Replace translation templates with HTML fragments
     *
     * @param array|string $body
     * @param bool $isJson
     * @return $this
     */
    public function processResponseBody(&$body, $isJson)
    {
        $this->_parser->setIsJson($isJson);
        if (!$this->isAllowed()) {
            return $this;
        }

        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->processResponseBody($part, $isJson);
            }
        } elseif (is_string($body)) {
            $content = $this->_parser->processResponseBodyString($body, $this);
            $this->_insertInlineScriptsHtml($content);
            $body = $this->_parser->getContent();
        }
        $this->_parser->setIsJson(\Magento\Translate\Inline\ParserInterface::JSON_FLAG_DEFAULT_STATE);
        return $this;
    }

    /**
     * Additional translation mode html attribute is not needed for base inline translation.
     *
     * @param mixed|string $tagName
     * @return string
     */
    public function getAdditionalHtmlAttribute($tagName = null)
    {
        return null;
    }

    /**
     * Create block to render script and html with added inline translation content.
     */
    protected function _insertInlineScriptsHtml($content)
    {
        if ($this->_isScriptInserted || stripos($content, '</body>') === false) {
            return;
        }

        /** @var $block \Magento\View\Element\Template */
        $block = $this->_layout->createBlock('Magento\View\Element\Template');

        $block->setAjaxUrl($this->_getAjaxUrl());

        $block->setTemplate('Magento_Core::translate_inline.phtml');

        $html = $block->toHtml();

        $this->_parser->setContent(str_ireplace('</body>', $html . '</body>', $content));

        $this->_isScriptInserted = true;
    }

    /**
     * Return URL for ajax requests
     *
     * @return string
     */
    protected function _getAjaxUrl()
    {
        $scope = $this->_scopeResolver->getScope();
        return $this->_url->getUrl('core/ajax/translate', array('_secure' => $scope->isCurrentlySecure()));
    }

    /**
     * Strip inline translations from text
     *
     * @param array|string $body
     * @return $this
     */
    protected function _stripInlineTranslations(&$body)
    {
        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->_stripInlineTranslations($part);
            }
        } else if (is_string($body)) {
            $body = preg_replace('#' . $this->_tokenRegex . '#', '$1', $body);
        }
        return $this;
    }
}
