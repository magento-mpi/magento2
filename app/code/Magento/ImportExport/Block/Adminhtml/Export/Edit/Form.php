<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export edit form block
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Block_Adminhtml_Export_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_ImportExport_Model_Source_Export_EntityFactory
     */
    protected $_entityFactory;

    /**
     * @var Magento_ImportExport_Model_Source_Export_FormatFactory
     */
    protected $_formatFactory;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_ImportExport_Model_Source_Export_EntityFactory $entityFactory
     * @param Magento_ImportExport_Model_Source_Export_FormatFactory $formatFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_ImportExport_Model_Source_Export_EntityFactory $entityFactory,
        Magento_ImportExport_Model_Source_Export_FormatFactory $formatFactory,
        array $data = array()
    ) {
        $this->_entityFactory = $entityFactory;
        $this->_formatFactory = $formatFactory;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare form before rendering HTML.
     *
     * @return Magento_ImportExport_Block_Adminhtml_Export_Edit_Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'     => 'edit_form',
                'action' => $this->getUrl('*/*/getFilter'),
                'method' => 'post',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Export Settings')));
        $fieldset->addField('entity', 'select', array(
            'name'     => 'entity',
            'title'    => __('Entity Type'),
            'label'    => __('Entity Type'),
            'required' => false,
            'onchange' => 'varienExport.getFilter();',
            'values'   => $this->_entityFactory->create()->toOptionArray()
        ));
        $fieldset->addField('file_format', 'select', array(
            'name'     => 'file_format',
            'title'    => __('Export File Format'),
            'label'    => __('Export File Format'),
            'required' => false,
            'values'   => $this->_formatFactory->create()->toOptionArray()
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
