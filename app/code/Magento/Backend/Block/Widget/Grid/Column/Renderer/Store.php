<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

/**
 * Store grid column filter
 */
class Store
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var bool
     */
    protected $_skipAllStoresLabel = false;

    /**
     * @var bool
     */
    protected $_skipEmptyStoresLabel = false;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve System Store model
     *
     * @return \Magento\Store\Model\System\Store
     */
    protected function _getStoreModel()
    {
        return $this->_systemStore;
    }

    /**
     * Retrieve 'show all stores label' flag
     *
     * @return bool
     */
    protected function _getShowAllStoresLabelFlag()
    {
        return $this->getColumn()->getData('skipAllStoresLabel')
            ? $this->getColumn()->getData('skipAllStoresLabel')
            : $this->_skipAllStoresLabel;
    }

    /**
     * Retrieve 'show empty stores label' flag
     *
     * @return bool
     */
    protected function _getShowEmptyStoresLabelFlag()
    {
        return $this->getColumn()->getData('skipEmptyStoresLabel')
            ? $this->getColumn()->getData('skipEmptyStoresLabel')
            : $this->_skipEmptyStoresLabel;
    }

    /**
     * Render row store views
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $out = '';
        $skipAllStoresLabel = $this->_getShowAllStoresLabelFlag();
        $skipEmptyStoresLabel = $this->_getShowEmptyStoresLabelFlag();
        $origStores = $row->getData($this->getColumn()->getIndex());

        if (is_null($origStores) && $row->getStoreName()) {
            $scopes = array();
            foreach (explode("\n", $row->getStoreName()) as $k => $label) {
                $scopes[] = str_repeat('&nbsp;', $k * 3) . $label;
            }
            $out .= implode('<br/>', $scopes) . __(' [deleted]');
            return $out;
        }

        if (empty($origStores) && !$skipEmptyStoresLabel) {
            return '';
        }
        if (!is_array($origStores)) {
            $origStores = array($origStores);
        }

        if (empty($origStores)) {
            return '';
        } elseif (in_array(0, $origStores) && count($origStores) == 1 && !$skipAllStoresLabel) {
            return __('All Store Views');
        }

        $data = $this->_getStoreModel()->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $out .= $website['label'] . '<br/>';
            foreach ($website['children'] as $group) {
                $out .= str_repeat('&nbsp;', 3) . $group['label'] . '<br/>';
                foreach ($group['children'] as $store) {
                    $out .= str_repeat('&nbsp;', 6) . $store['label'] . '<br/>';
                }
            }
        }

        return $out;
    }

    /**
     * Render row store views for export
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function renderExport(\Magento\Object $row)
    {
        $out = '';
        $skipAllStoresLabel = $this->_getShowAllStoresLabelFlag();
        $origStores = $row->getData($this->getColumn()->getIndex());

        if (is_null($origStores) && $row->getStoreName()) {
            $scopes = array();
            foreach (explode("\n", $row->getStoreName()) as $k => $label) {
                $scopes[] = str_repeat(' ', $k * 3) . $label;
            }
            $out .= implode("\r\n", $scopes) . __(' [deleted]');
            return $out;
        }

        if (!is_array($origStores)) {
            $origStores = array($origStores);
        }

        if (in_array(0, $origStores) && !$skipAllStoresLabel) {
            return __('All Store Views');
        }

        $data = $this->_getStoreModel()->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $out .= $website['label'] . "\r\n";
            foreach ($website['children'] as $group) {
                $out .= str_repeat(' ', 3) . $group['label'] . "\r\n";
                foreach ($group['children'] as $store) {
                    $out .= str_repeat(' ', 6) . $store['label'] . "\r\n";
                }
            }
        }

        return $out;
    }
}
