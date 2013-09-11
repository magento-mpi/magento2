<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Simple links list block
 *
 * @category   Magento
 * @package    Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Page\Block\Template;

class Links extends \Magento\Core\Block\Template
{
    /**
     * All links
     *
     * @var array
     */
    protected $_links = array();

    /**
     * Cache key info
     *
     * @var null|array
     */
    protected $_cacheKeyInfo = null;

    protected $_template = 'template/links.phtml';

    /**
     * Get all links
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->_links;
    }

    /**
     * Add link to the list
     *
     * @param string $label
     * @param string $url
     * @param string $title
     * @param boolean $prepare
     * @param array $urlParams
     * @param int $position
     * @param string|array $liParams
     * @param string|array $aParams
     * @param string $beforeText
     * @param string $afterText
     * @return \Magento\Page\Block\Template\Links
     */
    public function addLink($label, $url = '', $title = '', $prepare = true, $urlParams = array(),
        $position = null, $liParams = null, $aParams = null, $beforeText = '', $afterText = ''
    ) {
        if (is_null($label) || false === $label) {
            return $this;
        }
        $link = new \Magento\Object(array(
            'label'       => $label,
            'url'         => ($prepare ? $this->getUrl($url, (is_array($urlParams) ? $urlParams : array())) : $url),
            'title'       => $title,
            'li_params'   => $this->_prepareParams($liParams),
            'a_params'    => $this->_prepareParams($aParams),
            'before_text' => $beforeText,
            'after_text'  => $afterText,
        ));

        $this->_links[$this->_getNewPosition($position)] = $link;
        if (intval($position) > 0) {
            ksort($this->_links);
        }

        return $this;
    }

    /**
     * Add block to link list
     *
     * @param string $blockName
     * @return \Magento\Page\Block\Template\Links
     */
    public function addLinkBlock($blockName)
    {
        $block = $this->getLayout()->getBlock($blockName);
        if ($block) {
            $this->_links[$this->_getNewPosition((int)$block->getPosition())] = $block;
            ksort($this->_links);
        }
        return $this;
    }

    /**
     * Remove Link block by blockName
     *
     * @param string $blockName
     * @return \Magento\Page\Block\Template\Links
     */
    public function removeLinkBlock($blockName)
    {
        foreach ($this->_links as $key => $link) {
            if ($link instanceof \Magento\Core\Block\AbstractBlock && $link->getNameInLayout() == $blockName) {
                unset($this->_links[$key]);
            }
        }
        return $this;
    }

    /**
     * Removes link by url
     *
     * @param string $url
     * @return \Magento\Page\Block\Template\Links
     */
    public function removeLinkByUrl($url)
    {
        foreach ($this->_links as $k => $v) {
            if ($v->getUrl() == $url) {
                unset($this->_links[$k]);
            }
        }

        return $this;
    }

    /**
     * Get cache key informative items
     * Provide string array key to share specific info item with FPC placeholder
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        if (is_null($this->_cacheKeyInfo)) {
            $links = array();
            if (!empty($this->_links)) {
                foreach ($this->_links as $position => $link) {
                    if ($link instanceof \Magento\Object) {
                        $links[$position] = $link->getData();
                    }
                }
            }
            $this->_cacheKeyInfo = parent::getCacheKeyInfo() + array(
                'links' => base64_encode(serialize($links)),
                'name' => $this->getNameInLayout()
            );
        }

        return $this->_cacheKeyInfo;
    }

    /**
     * Prepare tag attributes
     *
     * @param string|array $params
     * @return string
     */
    protected function _prepareParams($params)
    {
        if (is_string($params)) {
            return $params;
        } elseif (is_array($params)) {
            $result = '';
            foreach ($params as $key=>$value) {
                $result .= ' ' . $key . '="' . addslashes($value) . '"';
            }
            return $result;
        }
        return '';
    }

    /**
     * Set first/last
     *
     * @return \Magento\Page\Block\Template\Links
     */
    protected function _beforeToHtml()
    {
        if (!empty($this->_links)) {
            reset($this->_links);
            $this->_links[key($this->_links)]->setIsFirst(true);
            end($this->_links);
            $this->_links[key($this->_links)]->setIsLast(true);
        }
        return parent::_beforeToHtml();
    }

    /**
     * Return new link position in list
     *
     * @param int $position
     * @return int
     */
    protected function _getNewPosition($position = 0)
    {
        if (intval($position) > 0) {
            while (isset($this->_links[$position])) {
                $position++;
            }
        } else {
            $position = 0;
            foreach ($this->_links as $k=>$v) {
                $position = $k;
            }
            $position += 10;
        }
        return $position;
    }

    /**
     * Render Block
     *
     * @param \Magento\Core\Block\AbstractBlock $block
     * @return string
     */
    public function renderBlock(\Magento\Core\Block\AbstractBlock $block)
    {
        return $this->getLayout()->renderElement($block->getNameInLayout());
    }
}
