<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalogsearch term block
 *
 * @category   Magento
 * @package    Magento_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogSearch\Block;

class Term extends \Magento\View\Element\Template
{
    protected $_terms;
    protected $_minPopularity;
    protected $_maxPopularity;

    /**
     * Url factory
     *
     * @var \Magento\UrlFactory
     */
    protected $_urlFactory;

    /**
     * Query collection factory
     *
     * @var \Magento\CatalogSearch\Model\Resource\Query\CollectionFactory
     */
    protected $_queryCollectionFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\CatalogSearch\Model\Resource\Query\CollectionFactory $queryCollectionFactory
     * @param \Magento\UrlFactory $urlFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\CatalogSearch\Model\Resource\Query\CollectionFactory $queryCollectionFactory,
        \Magento\UrlFactory $urlFactory,
        array $data = array()
    ) {
        $this->_queryCollectionFactory = $queryCollectionFactory;
        $this->_urlFactory = $urlFactory;
        parent::__construct($context, $data);
    }

    /**
     * Load terms and try to sort it by names
     *
     * @return \Magento\CatalogSearch\Block\Term
     */
    protected function _loadTerms()
    {
        if (empty($this->_terms)) {
            $this->_terms = array();
            $terms = $this->_queryCollectionFactory->create()
                ->setPopularQueryFilter($this->_storeManager->getStore()->getId())
                ->setPageSize(100)
                ->load()
                ->getItems();

            if( count($terms) == 0 ) {
                return $this;
            }


            $this->_maxPopularity = reset($terms)->getPopularity();
            $this->_minPopularity = end($terms)->getPopularity();
            $range = $this->_maxPopularity - $this->_minPopularity;
            $range = ( $range == 0 ) ? 1 : $range;
            foreach ($terms as $term) {
                if( !$term->getPopularity() ) {
                    continue;
                }
                $term->setRatio(($term->getPopularity()-$this->_minPopularity)/$range);
                $temp[$term->getName()] = $term;
                $termKeys[] = $term->getName();
            }
            natcasesort($termKeys);

            foreach ($termKeys as $termKey) {
                $this->_terms[$termKey] = $temp[$termKey];
            }
        }
        return $this;
    }

    public function getTerms()
    {
        $this->_loadTerms();
        return $this->_terms;
    }

    public function getSearchUrl($obj)
    {
        /** @var $url \Magento\UrlInterface */
        $url = $this->_urlFactory->create();
        /*
        * url encoding will be done in Url.php http_build_query
        * so no need to explicitly called urlencode for the text
        */
        $url->setQueryParam('q', $obj->getName());
        return $url->getUrl('catalogsearch/result');
    }

    public function getMaxPopularity()
    {
        return $this->_maxPopularity;
    }

    public function getMinPopularity()
    {
        return $this->_minPopularity;
    }
}
