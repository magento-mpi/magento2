<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget to display catalog link
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Widget;

use Magento\UrlRewrite\Service\V1\UrlMatcherInterface;

class Link extends \Magento\Framework\View\Element\Html\Link implements \Magento\Widget\Block\BlockInterface
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
     * Url matcher for category
     *
     * @var UrlMatcherInterface
     */
    protected $urlCategoryMatcher;

    /**
     * Url matcher for product
     *
     * @var UrlMatcherInterface
     */
    protected $urlProductMatcher;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param UrlMatcherInterface $urlMatcher
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        /** @TODO: UrlRewrite: Build product URL inside particular category */
        UrlMatcherInterface $urlCategoryMatcher,
        UrlMatcherInterface $urlProductMatcher,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->urlCategoryMatcher = $urlCategoryMatcher;
        $this->urlProductMatcher = $urlProductMatcher;
    }

    /**
     * Prepare url using passed id path and return it
     * or return false if path was not found in url rewrites.
     *
     * @throws \RuntimeException
     * @return string|false
     */
    public function getHref()
    {
        if ($this->_href === null) {
            if (!$this->getData('id_path')) {
                throw new \RuntimeException('Parameter id_path is not set.');
            }
            $rewriteData = $this->parseIdPath($this->getData('id_path'));

            $href = false;
            $store = $this->hasStoreId() ? $this->_storeManager->getStore($this->getStoreId())
                : $this->_storeManager->getStore();
            $filterData = [
                'entity_id'   => $rewriteData[1],
                'entity_type' => $rewriteData[0],
                'store_id'    => $store->getId(),
            ];
            if (!empty($rewriteData[2])) {
                $filterData['category_id'] = $rewriteData[2];
            }
            if ($rewriteData[0] == 'product') {
                $rewrite = $this->urlProductMatcher->findByData($filterData);
            } else {
                $rewrite = $this->urlCategoryMatcher->findByData($filterData);
            }


            if ($rewrite) {
                $href = $store->getUrl('', ['_direct' => $rewrite->getRequestPath()]);

                if (strpos($href, '___store') === false) {
                    $href .= (strpos($href, '?') === false ? '?' : '&') . '___store=' . $store->getCode();
                }
            }
            $this->_href = $href;
        }
        return $this->_href;
    }

    /**
     * Parse id_path
     *
     * @param string $idPath
     * @throws \RuntimeException
     * @return array
     */
    protected function parseIdPath($idPath)
    {
        $rewriteData = explode('/', $idPath);

        if (!isset($rewriteData[0]) || !isset($rewriteData[1])) {
            throw new \RuntimeException('Wrong id_path structure.');
        }
        return $rewriteData;
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
