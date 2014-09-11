<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document;

class Filter extends \Magento\Framework\Filter\Template
{
    /**
     * Modifier Callbacks
     *
     * @var array
     */
    protected $modifiers = array('nl2br' => '');

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper = null;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Layout directive params
     *
     * @var array
     */
    protected $directiveParams;

    /**
     * @var array
     */
    protected $dictionary = [];

    /**
     * @param \Magento\Framework\Stdlib\String $string
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param array $variables
     */
    public function __construct(
        \Magento\Framework\Stdlib\String $string,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\StoreManagerInterface $storeManager,
        $variables = []
    ) {
        $this->escaper = $escaper;
        $this->assetRepo = $assetRepo;
        $this->logger = $logger;
        $this->modifiers['escape'] = [$this, 'modifierEscape'];
        $this->storeManager = $storeManager;

        parent::__construct($string, $variables);
    }

    /**
     * Retrieve View URL directive
     *
     * @param string[] $construction
     * @return string
     */
    public function viewDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $url = $this->assetRepo->getUrlWithParams($params['url'], $params);
        return $url;
    }

    /**
     * Retrieve image
     *
     * @param string[] $construction
     * @return string
     */
    public function imageDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $url = $this->assetRepo->getUrlWithParams($params['url'], $params);
        $attributes = [];
        foreach (['width', 'height', 'style', 'alt'] as $attribute) {
            if (isset($params[$attribute])) {
                $attributes[] = $attribute . '="' . $params[$attribute] . '"';
            }
        }
        return '<img src="' . $url . '" ' . implode(' ', $attributes) . ' />';
    }

    /**
     * Retrieve url link
     *
     * @param string[] $construction
     * @return string
     */
    public function urlDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $url = $this->getUrl($params['path'], []);
        return '<a href="' . $url . '">' . $params['text'] . '</a>';
    }

    /**
     * Retrieve media file URL directive
     *
     * @param string[] $construction
     * @return string
     */
    public function mediaDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        return $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $params['url'];
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    protected function getUrl($path, $params)
    {
        return $this->storeManager->getStore()->getUrl($path, $params);
    }

    /**
     * Directive for converting special characters to HTML entities
     * Supported options:
     *     allowed_tags - Comma separated html tags that have not to be converted
     *
     * @param string[] $construction
     * @return string
     */
    public function escapehtmlDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        if (!isset($params['var'])) {
            return '';
        }

        $allowedTags = null;
        if (isset($params['allowed_tags'])) {
            $allowedTags = preg_split('/\s*\,\s*/', $params['allowed_tags'], 0, PREG_SPLIT_NO_EMPTY);
        }

        return $this->escaper->escapeHtml($params['var'], $allowedTags);
    }

    /**
     * Var directive with modifiers support
     *
     * @param string[] $construction
     * @return string
     */
    public function varDirective($construction)
    {
        if (count($this->_templateVars) == 0) {
            // If template preprocessing
            return $construction[0];
        }

        $parts = explode('|', $construction[2], 2);
        if (2 === count($parts)) {
            list($variableName, $modifiersString) = $parts;
            return $this->_amplifyModifiers($this->_getVariable($variableName, ''), $modifiersString);
        }
        return $this->_getVariable($construction[2], '');
    }

    /**
     * Apply modifiers one by one, with specified params
     *
     * Modifier syntax: modifier1[:param1:param2:...][|modifier2:...]
     *
     * @param string $value
     * @param string $modifiers
     * @return string
     */
    protected function _amplifyModifiers($value, $modifiers)
    {
        foreach (explode('|', $modifiers) as $part) {
            if (empty($part)) {
                continue;
            }
            $params = explode(':', $part);
            $modifier = array_shift($params);
            if (isset($this->modifiers[$modifier])) {
                $callback = $this->modifiers[$modifier];
                if (!$callback) {
                    $callback = $modifier;
                }
                array_unshift($params, $value);
                $value = call_user_func_array($callback, $params);
            }
        }
        return $value;
    }

    /**
     * Escape specified string
     *
     * @param string $value
     * @param string $type
     * @return string
     */
    public function modifierEscape($value, $type = 'html')
    {
        switch ($type) {
            case 'html':
                return htmlspecialchars($value, ENT_QUOTES);

            case 'htmlentities':
                return htmlentities($value, ENT_QUOTES);

            case 'url':
                return rawurlencode($value);
        }
        return $value;
    }

    /**
     * @param string $value
     * @param array $dictionary
     * @return mixed|string
     */
    public function preProcess($value, array $dictionary = [])
    {
        $this->dictionary = $dictionary;
        try {
            $value = $this->filter($value);
        } catch (\Exception $e) {
            $value = '';
            $this->logger->logException($e);
        }
        return $value;
    }
}
