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

use Magento\BaseScopeInterface;

class Inline implements \Magento\Translate\InlineInterface
{
    /**
     * Indicator to hold state of whether inline translation is allowed
     *
     * @var bool
     */
    protected $_isAllowed;

    /**
     * @var \Magento\Translate\Inline\ParserFactory
     */
    protected $_parserFactory;

    /**
     * Flag about inserted styles and scripts for inline translates
     *
     * @var bool
     */
    protected $_isScriptInserted = false;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Translate\Inline\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\BaseScopeResolverInterface
     */
    protected $_scopeResolver;

    /**
     * @var string
     */
    protected $_templateFileName;

    /**
     * @var string
     */
    protected $_translatorRoute;

    /**
     * @var null|string
     */
    protected $scope;

    /**
     * @var Inline\StateInterface
     */
    protected $state;

    /**
     * @param \Magento\BaseScopeResolverInterface $scopeResolver
     * @param \Magento\UrlInterface $url
     * @param \Magento\View\LayoutInterface $layout
     * @param Inline\ConfigInterface $config
     * @param Inline\ParserFactory $parserFactory
     * @param Inline\StateInterface $state
     * @param string $templateFileName
     * @param string $translatorRoute
     * @param null $scope
     */
    public function __construct(
        \Magento\BaseScopeResolverInterface $scopeResolver,
        \Magento\UrlInterface $url,
        \Magento\View\LayoutInterface $layout,
        \Magento\Translate\Inline\ConfigInterface $config,
        \Magento\Translate\Inline\ParserFactory $parserFactory,
        \Magento\Translate\Inline\StateInterface $state,
        $templateFileName = '',
        $translatorRoute = '',
        $scope = null
    ) {
        $this->_scopeResolver = $scopeResolver;
        $this->_url = $url;
        $this->_layout = $layout;
        $this->config = $config;
        $this->_parserFactory = $parserFactory;
        $this->state = $state;
        $this->_templateFileName = $templateFileName;
        $this->_translatorRoute = $translatorRoute;
        $this->scope = $scope;
    }

    /**
     * Check if Inline Translates is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        if ($this->_isAllowed === null) {
            if (!$this->scope instanceof BaseScopeInterface) {
                $scope = $this->_scopeResolver->getScope($this->scope);
            }
            $this->_isAllowed = $this->config->isActive($scope)
                && $this->config->isDevAllowed($scope);
        }
        return $this->state->isEnabled() && $this->_isAllowed;
    }

    /**
     * Retrieve Inline Parser instance
     *
     * @return Inline\ParserInterface
     */
    public function getParser()
    {
        return $this->_parserFactory->get();
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
        if (!$this->_isScriptInserted) {
            $this->getParser()->setContent(str_ireplace('</body>', $this->getInlineScript() . '</body>', $content));
            $this->_isScriptInserted = true;
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
        $block = $this->_layout->createBlock('Magento\View\Element\Template');

        $block->setAjaxUrl($this->getAjaxUrl());
        $block->setTemplate($this->_templateFileName);

        return $block->toHtml();
    }

    /**
     * Return URL for ajax requests
     *
     * @return string
     */
    protected function getAjaxUrl()
    {
        return $this->_url->getUrl(
            $this->_translatorRoute,
            ['_secure' => $this->_scopeResolver->getScope()->isCurrentlySecure()]
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
