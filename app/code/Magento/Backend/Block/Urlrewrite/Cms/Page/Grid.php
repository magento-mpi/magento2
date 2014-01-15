<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CMS pages grid for URL rewrites
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Urlrewrite\Cms\Page;

class Grid extends \Magento\Cms\Block\Adminhtml\Page\Grid
{
    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
    }

    /**
     * Disable massaction
     *
     * @return \Magento\Backend\Block\Urlrewrite\Cms\Page\Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Prepare columns layout
     *
     * @return \Magento\Backend\Block\Urlrewrite\Cms\Page\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('title', array(
            'header' => __('Title'),
            'align'  => 'left',
            'index'  => 'title',
        ));

        $this->addColumn('identifier', array(
            'header' => __('URL Key'),
            'align'  => 'left',
            'index'  => 'identifier'
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'                    => __('Store View'),
                'index'                     => 'store_id',
                'type'                      => 'store',
                'store_all'                 => true,
                'store_view'                => true,
                'sortable'                  => false,
                'filter_condition_callback' => array($this, '_filterStoreCondition'),
            ));
        }

        $this->addColumn('is_active', array(
            'header'  => __('Status'),
            'index'   => 'is_active',
            'type'    => 'options',
            'options' => $this->_cmsPage->getAvailableStatuses()
        ));

        return $this;
    }

    /**
     * Get URL for dispatching grid ajax requests
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/*/cmsPageGrid', array('_current' => true));
    }

    /**
     * Return row url for js event handlers
     *
     * @param \Magento\Cms\Model\Page|\Magento\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/*/edit', array('cms_page' => $row->getId()));
    }
}
