<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model\Translate;

/**
 * Inline translation specific to Vde.
 */
class InlineVde implements \Magento\Core\Model\Translate\InlineInterface
{
    /**
     * data-translate-mode attribute name
     */
    const TRANSLATE_MODE = 'data-translate-mode';

    /**
     * text translate mode
     */
    const MODE_TEXT = 'text';

    /**
     * img element name
     */
    const ELEMENT_IMG = 'img';

    /**
     * alt translate mode
     */
    const MODE_ALT = 'alt';

    /**
     * script translate mode
     */
    const MODE_SCRIPT = 'script';

    /**
     * script element name
     */
    const ELEMENT_SCRIPT = self::MODE_SCRIPT;

    /**
     * @var \Magento\DesignEditor\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Core\Model\Translate\InlineParser
     */
    protected $_parser;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_url;

    /**
     * Flag about inserted styles and scripts for inline translates
     *
     * @var bool
     */
    protected $_isScriptInserted = false;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize inline translation model specific for vde
     *
     * @param \Magento\Core\Model\Translate\InlineParser $parser
     * @param \Magento\DesignEditor\Helper\Data $helper
     * @param \Magento\UrlInterface $url
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\Core\Model\Translate\InlineParser $parser,
        \Magento\DesignEditor\Helper\Data $helper,
        \Magento\UrlInterface $url,
        \Magento\ObjectManager $objectManager
    ) {
        $this->_parser = $parser;
        $this->_helper = $helper;
        $this->_url = $url;
        $this->_objectManager = $objectManager;
    }

    /**
     * Translation within the vde will be enabled by the client when the 'Edit' button is enabled.
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_helper->isAllowed();
    }

    /**
     * Replace VDE specific translation templates with HTML fragments
     *
     * @param string[]|string &$body
     * @param bool $isJson
     * @return $this
     */
    public function processResponseBody(&$body, $isJson)
    {
        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->processResponseBody($part, $isJson);
            }
        } elseif (is_string($body)) {
            $content = $this->_parser->processResponseBodyString($body, $this);
            $this->_insertInlineScriptsHtml($content);
            $body = $this->_parser->getContent();
        }
        return $this;
    }

    /**
     * Returns the translation mode html attribute needed by vde to specify which translation mode the
     * element represents.
     *
     * @param string|null $tagName
     * @return string
     */
    public function getAdditionalHtmlAttribute($tagName = null)
    {
        return self::TRANSLATE_MODE . '="' . $this->_getTranslateMode($tagName) . '"';
    }

    /**
     * Create block to render script and html with added inline translation content specific for vde.
     *
     * @param string $content
     * @return void
     */
    private function _insertInlineScriptsHtml($content)
    {
        if ($this->_isScriptInserted || stripos($content, '</body>') === false) {
            return;
        }

        $store = $this->_parser->getStoreManager()->getStore();
        $ajaxUrl = $this->_url->getUrl('core/ajax/translate', array(
            '_secure' => $store->isCurrentlySecure(),
            \Magento\DesignEditor\Helper\Data::TRANSLATION_MODE => $this->_helper->getTranslationMode()
        ));

        /** @var $block \Magento\View\Element\Template */
        $block = $this->_objectManager->create('Magento\View\Element\Template');

        $block->setArea($this->_parser->getDesignPackage()->getArea());
        $block->setAjaxUrl($ajaxUrl);

        $block->setFrameUrl($this->_getFrameUrl());
        $block->setRefreshCanvas($this->isAllowed());

        $block->setTemplate('Magento_DesignEditor::translate_inline.phtml');
        $block->setTranslateMode($this->_helper->getTranslationMode());

        $this->_parser->setContent(str_ireplace('</body>', $block->toHtml() . '</body>', $content));

        $this->_isScriptInserted = true;
    }

    /**
     * Generate frame url
     *
     * @return string
     */
    protected function _getFrameUrl()
    {
        /** @var \Magento\Backend\Model\Session $backendSession */
        $backendSession = $this->_objectManager->get('Magento\Backend\Model\Session');

        /** @var $vdeUrlModel \Magento\DesignEditor\Model\Url\NavigationMode */
        $vdeUrlModel = $this->_objectManager->create('Magento\DesignEditor\Model\Url\NavigationMode');
        $currentUrl = $backendSession->getData(\Magento\DesignEditor\Model\State::CURRENT_URL_SESSION_KEY);

        return $vdeUrlModel->getUrl(ltrim($currentUrl, '/'));
    }

    /**
     * Get inline vde translate mode
     *
     * @param string  $tagName
     * @return string
     */
    private function _getTranslateMode($tagName)
    {
        $mode = self::MODE_TEXT;
        if (self::ELEMENT_SCRIPT == $tagName) {
            $mode = self::MODE_SCRIPT;
        } elseif (self::ELEMENT_IMG == $tagName) {
            $mode = self::MODE_ALT;
        }
        return $mode;
    }
}
