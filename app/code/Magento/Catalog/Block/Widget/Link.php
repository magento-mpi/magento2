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
 * Widget to display catalog link
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Widget;

class Link extends \Magento\View\Element\Html\Link implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Entity model name which must be used to retrieve entity specific data.
     * @var null|\Magento\Catalog\Model\Resource\AbstractResource
     */
    protected $_entityResource = null;

    /**
     * Prepared href attribute
     *
     * @var string
     */
    protected $_href;

    /**
     * Prepared anchor text
     *
     * @var string
     */
    protected $_anchorText;

    /**
     * Url rewrite
     *
     * @var \Magento\Core\Model\Resource\Url\Rewrite
     */
    protected $_urlRewrite;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Resource\Url\Rewrite $urlRewrite
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Model\Resource\Url\Rewrite $urlRewrite,
        array $data = array()
    ) {
        $this->_urlRewrite = $urlRewrite;
        parent::__construct($context, $data);
    }

    /**
     * Prepare url using passed id path and return it
     * or return false if path was not found in url rewrites.
     *
     * @return string|false
     */
    public function getHref()
    {
        if (!$this->_href) {

            if ($this->hasStoreId()) {
                $store = $this->_storeManager->getStore($this->getStoreId());
            } else {
                $store = $this->_storeManager->getStore();
            }

            /* @var $store \Magento\Store\Model\Store */
            $href = "";
            if ($this->getData('id_path')) {
                $href = $this->_urlRewrite->getRequestPathByIdPath($this->getData('id_path'), $store);
                if (!$href) {
                    return false;
                }
            }

            $this->_href = $store->getUrl('', array('_direct' => $href));
        }

        if (strpos($this->_href, "___store") === false) {
            $symbol = strpos($this->_href, "?") === false ? "?" : "&";
            $this->_href = $this->_href . $symbol . "___store=" . $store->getCode();
        }

        return $this->_href;
    }

    /**
     * Prepare label using passed text as parameter.
     * If anchor text was not specified get entity name from DB.
     *
     * @return string
     */
    public function getLabel()
    {
        if (!$this->_anchorText && $this->_entityResource) {
            if (!$this->getData('label')) {
                $idPath = explode('/', $this->_getData('id_path'));
                if (isset($idPath[1])) {
                    $id = $idPath[1];
                    if ($id) {
                        $this->_anchorText = $this->_entityResource->getAttributeRawValue(
                            $id,
                            'name',
                            $this->_storeManager->getStore()
                        );
                    }
                }
            } else {
                $this->_anchorText = $this->getData('label');
            }
        }

        return $this->_anchorText;
    }

    /**
     * Render block HTML
     * or return empty string if url can't be prepared
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getHref()) {
            return parent::_toHtml();
        }
        return '';
    }
}
