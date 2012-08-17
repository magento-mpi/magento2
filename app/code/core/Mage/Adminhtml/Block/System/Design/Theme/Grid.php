<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Themes grid
 */
class Mage_Adminhtml_Block_System_Design_Theme_Grid extends Mage_Backend_Block_Widget_Grid
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('themeGrid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid data collection
     *
     * @return Mage_Adminhtml_Block_System_Design_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = Mage::getResourceModel('Mage_Core_Model_Resource_Theme_Collection');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Define grid columns
     *
     * @return Mage_Adminhtml_Block_System_Design_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('area', array(
            'header'    => Mage::helper('Mage_Core_Helper_Data')->__('Area'),
            //'width'     => '150px',
            'index'     => 'theme_area',
        ));

        $this->addColumn('package', array(
            'header'    => Mage::helper('Mage_Core_Helper_Data')->__('Package'),
            //'width'     => '150px',
            'index'     => 'theme_package',
        ));

        $this->addColumn('code', array(
            'header'    => Mage::helper('Mage_Core_Helper_Data')->__('Code'),
            //'width'     => '150px',
            'index'     => 'theme_code',
        ));


        $this->addColumn('skin', array(
            'header'    => Mage::helper('Mage_Core_Helper_Data')->__('Skin'),
            //'width'     => '150px',
            'index'     => 'theme_skin',
        ));

        $this->addColumn('action',
            array(
                'header'   => Mage::helper('Mage_Core_Helper_Data')->__('Action'),
                'width'    => '100px',
                'type'     => 'action',
                'getter'   => 'getId',
                'actions'  => array(
                    array(
                        'caption' => Mage::helper('Mage_Core_Helper_Data')->__('Edit'),
                        'url'     => array('base' => '*/*/edit'),
                        'field'   => 'id'
                    ),
                    array (
                        'caption' => Mage::helper('Mage_Core_Helper_Data')->__('Delete'),
                        'url'     => array('base' => '*/*/delete'),
                        'field'   => 'id'
                    )
                ),
                'filter'   => false,
                'sortable' => false,
                'index'    => 'theme',
            ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare row click url
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * Prepare grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}
