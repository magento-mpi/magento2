<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layered navigation state
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\LayeredNavigation\Block\Navigation;

use Magento\Framework\View\Element\Template;

class State extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'layer/state.phtml';

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer $catalogLayer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer $catalogLayer,
        array $data = array()
    ) {
        $this->_catalogLayer = $catalogLayer;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $filters = $this->getLayer()->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = array();
        }
        return $filters;
    }

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        $filterState = array();
        foreach ($this->getActiveFilters() as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $filterState;
        $params['_escape'] = true;
        return $this->_urlBuilder->getUrl('*/*/*', $params);
    }

    /**
     * Retrieve Layer object
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        if (!$this->hasData('layer')) {
            $this->setLayer($this->_catalogLayer);
        }
        return $this->_getData('layer');
    }
}
