<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Block\Html;

/**
 * Html page breadcrumbs block
 */
class Breadcrumbs extends \Magento\Framework\View\Element\Template
{
    /**
     * Current template name
     *
     * @var string
     */
    protected $_template = 'html/breadcrumbs.phtml';

    /**
     * List of available breadcrumb properties
     *
     * @var string[]
     */
    protected $_properties = array('label', 'title', 'link', 'first', 'last', 'readonly');

    /**
     * List of breadcrumbs
     *
     * @var array
     */
    protected $_crumbs;

    /**
     * Cache key info
     *
     * @var null|array
     */
    protected $_cacheKeyInfo;

    /**
     * Add crumb
     *
     * @param string $crumbName
     * @param array $crumbInfo
     * @return $this
     */
    public function addCrumb($crumbName, $crumbInfo)
    {
        foreach ($this->_properties as $key) {
            if (!isset($crumbInfo[$key])) {
                $crumbInfo[$key] = null;
            }
        }

        if (!isset($this->_crumbs[$crumbName]) || !$this->_crumbs[$crumbName]['readonly']) {
            $this->_crumbs[$crumbName] = $crumbInfo;
        }

        return $this;
    }

    /**
     * Get cache key informative items
     *
     * Provide string array key to share specific info item with FPC placeholder
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        if (is_null($this->_cacheKeyInfo)) {
            $this->_cacheKeyInfo = parent::getCacheKeyInfo() + array(
                'crumbs' => base64_encode(serialize($this->_crumbs)),
                'name' => $this->getNameInLayout()
            );
        }
        return $this->_cacheKeyInfo;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (is_array($this->_crumbs)) {
            reset($this->_crumbs);
            $this->_crumbs[key($this->_crumbs)]['first'] = true;
            end($this->_crumbs);
            $this->_crumbs[key($this->_crumbs)]['last'] = true;
        }
        $this->assign('crumbs', $this->_crumbs);

        return parent::_toHtml();
    }
}
