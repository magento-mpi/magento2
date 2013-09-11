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
     * @var \Magento\Core\Model\View\Url
     */
    protected $_viewUrl;

    /**
     * @param \Magento\Core\Model\View\Url $viewUrl
     */
    public function __construct(\Magento\Core\Model\View\Url $viewUrl)
    {
        $this->_viewUrl = $viewUrl;
    }

    /**
     * Set use absolute links flag
     *
     * @param bool $flag
     * @return \Magento\Core\Model\Email\Template\Filter
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
     * @return \Magento\Core\Model\Email\Template\Filter
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
     * @see \Magento\Core\Model\Email\Template\Filter::viewDirective() method has been copypasted
     */
    public function viewDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $params['_absolute'] = $this->_useAbsoluteLinks;

        $url = $this->_viewUrl->getViewFileUrl($params['url'], $params);

        return $url;
    }

    /**
     * Retrieve media file URL directive
     *
     * @param array $construction
     * @return string
     * @see \Magento\Core\Model\Email\Template\Filter::mediaDirective() method has been copypasted
     */
    public function mediaDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        return \Mage::getBaseUrl('media') . $params['url'];
    }

    /**
     * Retrieve store URL directive
     * Support url and direct_url properties
     *
     * @param array $construction
     * @return string
     * @see \Magento\Core\Model\Email\Template\Filter::storeDirective() method has been copypasted
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

        return \Mage::app()->getStore()->getUrl($path, $params);
    }
}
