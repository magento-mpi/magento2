<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Template Filter Model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 * @todo        Needs to be reimplemented to get rid of the copypasted methods
 */
namespace Magento\Catalog\Model\Template;

class Filter extends \Magento\Filter\Template
{
    /**
     * Use absolute links flag
     *
     * @var bool
     */
    protected $_useAbsoluteLinks = false;

    /**
     * Whether to allow SID in store directive: NO
     *
     * @var bool
     */
    protected $_useSessionInUrl = false;

    /**
     * @var \Magento\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Stdlib\String $string
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\View\Asset\Repository $assetRepo
     * @param array $variables
     */
    public function __construct(
        \Magento\Stdlib\String $string,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\View\Asset\Repository $assetRepo,
        $variables = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_assetRepo = $assetRepo;
        parent::__construct($string, $variables);
    }

    /**
     * Set use absolute links flag
     *
     * @param bool $flag
     * @return \Magento\Email\Model\Template\Filter
     */
    public function setUseAbsoluteLinks($flag)
    {
        $this->_useAbsoluteLinks = $flag;
        return $this;
    }

    /**
     * Setter whether SID is allowed in store directive
     * Doesn't set anything intentionally, since SID is not allowed in any kind of emails
     *
     * @param bool $flag
     * @return \Magento\Email\Model\Template\Filter
     */
    public function setUseSessionInUrl($flag)
    {
        $this->_useSessionInUrl = $flag;
        return $this;
    }

    /**
     * Retrieve View URL directive
     *
     * @param array $construction
     * @return string
     * @see \Magento\Email\Model\Template\Filter::viewDirective() method has been copypasted
     */
    public function viewDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $params['_absolute'] = $this->_useAbsoluteLinks;
        /**
         * @bug: the "_absolute" key is not supported by underlying services
         * probably this happened because of multitude of refactorings in past
         * The original intent of _absolute parameter was to simply append specified path to a base URL
         * bypassing any kind of processing.
         * For example, normally you would use {{view url="css/styles.css"}} directive which would automatically resolve
         * into something like http://example.com/pub/static/area/theme/en_US/css/styles.css
         * But with _absolute, the expected behavior is this: {{view url="favicon.ico" _absolute=true}} should resolve
         * into something like http://example.com/favicon.ico
         *
         * To fix the issue, it is better not to maintain the _absolute parameter anymore in undrelying services,
         * but instead just create a different type of directive, for example {{baseUrl path="favicon.ico"}}
         */
        $url = $this->_assetRepo->getUrlWithParams($params['url'], $params);

        return $url;
    }

    /**
     * Retrieve media file URL directive
     *
     * @param array $construction
     * @return string
     * @see \Magento\Email\Model\Template\Filter::mediaDirective() method has been copypasted
     */
    public function mediaDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\UrlInterface::URL_TYPE_MEDIA) . $params['url'];
    }

    /**
     * Retrieve store URL directive
     * Support url and direct_url properties
     *
     * @param array $construction
     * @return string
     * @see \Magento\Email\Model\Template\Filter::storeDirective() method has been copypasted
     */
    public function storeDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        if (!isset($params['_query'])) {
            $params['_query'] = array();
        }
        foreach ($params as $key => $value) {
            if (strpos($key, '_query_') === 0) {
                $params['_query'][substr($key, 7)] = $value;
                unset($params[$key]);
            }
        }
        $params['_absolute'] = $this->_useAbsoluteLinks;

        if ($this->_useSessionInUrl === false) {
            $params['_nosid'] = true;
        }

        if (isset($params['direct_url'])) {
            $path = '';
            $params['_direct'] = $params['direct_url'];
            unset($params['direct_url']);
        } else {
            $path = isset($params['url']) ? $params['url'] : '';
            unset($params['url']);
        }

        return $this->_storeManager->getStore()->getUrl($path, $params);
    }
}
