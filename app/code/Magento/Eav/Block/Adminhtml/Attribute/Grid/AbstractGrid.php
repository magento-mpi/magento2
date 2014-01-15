<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attributes grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Block\Adminhtml\Attribute\Grid;

abstract class AbstractGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Block Module
     *
     * @var string
     */
    protected $_module = 'adminhtml';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('attributeGrid');
        $this->setDefaultSort('attribute_code');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare default grid column
     *
     * @return \Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('attribute_code', array(
            'header'=>__('Attribute Code'),
            'sortable'=>true,
            'index'=>'attribute_code',
            'header_css_class'  => 'col-attr-code',
            'column_css_class'  => 'col-attr-code'
        ));

        $this->addColumn('frontend_label', array(
            'header'=>__('Attribute Label'),
            'sortable'=>true,
            'index'=>'frontend_label',
            'header_css_class'  => 'col-label',
            'column_css_class'  => 'col-label'
        ));

        $this->addColumn('is_required', array(
            'header'=>__('Required'),
            'sortable'=>true,
            'index'=>'is_required',
            'type' => 'options',
            'options' => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
            'header_css_class'  => 'col-required',
            'column_css_class'  => 'col-required'
        ));

        $this->addColumn('is_user_defined', array(
            'header'=>__('System'),
            'sortable'=>true,
            'index'=>'is_user_defined',
            'type' => 'options',
            'options' => array(
                '0' => __('Yes'),   // intended reverted use
                '1' => __('No'),    // intended reverted use
            ),
            'header_css_class'  => 'col-system',
            'column_css_class'  => 'col-system'
        ));

        return $this;
    }

    /**
     * Return url of given row
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl($this->_module . '/*/edit', array('attribute_id' => $row->getAttributeId()));
    }

}
