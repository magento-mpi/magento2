<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Form Types Grid Block
 *
 * @category   Magento
 * @package    Magento_CustomerCustomAttributes
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * Initialize Grid Block
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('code');
        $this->setDefaultDir('asc');
    }

    /**
     * Prepare grid collection object
     *
     * @return \Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Grid
     */
    protected function _prepareCollection()
    {
        $collection = \Mage::getModel('Magento\Eav\Model\Form\Type')
            ->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return \Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('code', array(
            'header'    => __('Type Code'),
            'index'     => 'code',
        ));

        $this->addColumn('label', array(
            'header'    => __('Label'),
            'index'     => 'label',
        ));

        $this->addColumn('store_id', array(
            'header'    => __('Store View'),
            'index'     => 'store_id',
            'type'      => 'store'
        ));

        /** @var $label \Magento\Core\Model\Theme\Label */
        $label = \Mage::getModel('Magento\Core\Model\Theme\Label');
        $design = $label->getLabelsCollection();
        array_unshift($design, array(
            'value' => 'all',
            'label' => __('All Themes')
        ));
        $this->addColumn('theme', array(
            'header'     => __('Theme'),
            'type'       => 'theme',
            'index'      => 'theme',
            'options'    => $design,
            'with_empty' => true,
            'default'    => __('All Themes')
        ));

        $this->addColumn('is_system', array(
            'header'    => __('System'),
            'index'     => 'is_system',
            'type'      => 'options',
            'options'   => array(
                0 => __('No'),
                1 => __('Yes'),
            )
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve row click URL
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('type_id' => $row->getId()));
    }
}
