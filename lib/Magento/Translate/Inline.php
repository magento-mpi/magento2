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
     * Indicator to hold state of whether inline translation is allowed
     *
     * @var bool
     */
    protected $isAllowed;

    /**
     * @var \Magento\Translate\Inline\ParserInterface
     */
    protected $parser;

    /**
     * Flag about inserted styles and scripts for inline translates
     *
     * @var bool
     */
    protected $isScriptInserted = false;

    /**
     * @var \Magento\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Translate\Inline\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\App\ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * @var string
     */
    protected $templateFileName;

    /**
     * @var string
     */
    protected $translatorRoute;

    /**
     * @var null|string
     */
    protected $scope;

    /**
     * @var Inline\StateInterface
     */
    protected $state;

    /**
     * Initialize inline translation model
     *
     * @param \Magento\App\ScopeResolverInterface $scopeResolver
     * @param \Magento\Translate\Inline\ParserFactory $parserFactory
     * @param \Magento\TranslateInterface $translate
     * @param \Magento\UrlInterface $url
     * @param \Magento\View\LayoutInterface $layout
     * @param Inline\ConfigInterface $config
     * @param Inline\ParserInterface $parser
     * @param Inline\StateInterface $state
     * @param string $templateFileName
     * @param string $translatorRoute
     * @param null $scope
     */
    public function __construct(
        \Magento\App\ScopeResolverInterface $scopeResolver,
        \Magento\Translate\Inline\ParserFactory $parserFactory,
        \Magento\TranslateInterface $translate,
        \Magento\UrlInterface $url,
        \Magento\View\LayoutInterface $layout,
        \Magento\Translate\Inline\ConfigInterface $config,
        \Magento\Translate\Inline\ParserInterface $parser,
        \Magento\Translate\Inline\StateInterface $state,
        $templateFileName = '',
        $translatorRoute = '',
        $scope = null
    ) {
        $this->scopeResolver = $scopeResolver;
        $this->url = $url;
        $this->layout = $layout;
        $this->config = $config;
        $this->parser = $parser;
        $this->state = $state;
        $this->templateFileName = $templateFileName;
        $this->translatorRoute = $translatorRoute;
        $this->scope = $scope;
    }

    /**
     * Check if Inline Translates is allowed
     *
     * @param \Magento\App\ScopeInterface|int|null $scope
     * @return bool
     */
    public function isAllowed($scope)
    {
        if (is_null($this->_isAllowed)) {
            if (!$scope instanceof \Magento\App\ScopeInterface) {
                $scope = $this->_scopeResolver->getScope($scope);
            }
            $this->isAllowed = $this->config->isActive($scope)
                && $this->config->isDevAllowed($scope);
        }
        return $this->state->isEnabled() && $this->isAllowed;
    }

    /**
     * Retrieve Inline Parser instance
     *
     * @return Inline\ParserInterface
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Replace translation templates with HTML fragments
     *
     * @param array|string &$body
     * @param bool $isJson
     * @return $this
     */
    public function processResponseBody(&$body, $isJson = false)
    {
        if ($this->scope == 'admin' && !$this->isAllowed()) {
            $this->stripInlineTranslations($body);
            return $this;
        }

        $this->getParser()->setIsJson($isJson);

        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->processResponseBody($part, $isJson);
            }
        } elseif (is_string($body)) {
            $this->getParser()->processResponseBodyString($body);
            $this->addInlineScript();
            $body = $this->getParser()->getContent();
        }

        $this->getParser()->setIsJson(false);

        return $this;
    }

    /**
     * Additional translation mode html attribute is not needed for base inline translation.
     *
     * @param mixed|string|null $tagName
     * @return null
     */
    public function getAdditionalHtmlAttribute($tagName = null)
    {
        return null;
    }

    /**
     * Add inline script code
     *
     * Insert script and html with
     * added inline translation content.
     *
     * @return void
     */
    protected function addInlineScript()
    {
        $content = $this->getParser()->getContent();
        if (stripos($content, '</body>') === false) {
            return;
        }
        if (!$this->isScriptInserted) {
            $this->getParser()->setContent(str_ireplace('</body>', $this->getInlineScript() . '</body>', $content));
            $this->isScriptInserted = true;
        }
    }

    /**
     * Retrieve inline script code
     *
     * Create block to render script and html with
     * added inline translation content.
     *
     * @return string
     */
    protected function getInlineScript()
    {
        /** @var $block \Magento\View\Element\Template */
        $block = $this->layout->createBlock('Magento\View\Element\Template');

        $block->setAjaxUrl($this->getAjaxUrl());
        $block->setTemplate($this->templateFileName);

        return $block->toHtml();
    }

    /**
     * Return URL for ajax requests
     *
     * @return string
     */
    protected function getAjaxUrl()
    {
        return $this->url->getUrl(
            $this->translatorRoute,
            ['_secure' => $this->scopeResolver->getScope()->isCurrentlySecure()]
        );
    }

    /**
     * Strip inline translations from text
     *
     * @param array|string &$body
     * @return $this
     */
    protected function stripInlineTranslations(&$body)
    {
        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->stripInlineTranslations($part);
            }
        } else if (is_string($body)) {
            $body = preg_replace('#' . \Magento\Translate\Inline\ParserInterface::REGEXP_TOKEN . '#', '$1', $body);
        }
        return $this;
    }
}
