<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend system config array field renderer
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\System\Config\Form\Field;

class Regexceptions
    extends \Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @param \Magento\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\App $application
     * @param array $data
     */
    public function __construct(
        \Magento\Data\Form\Element\Factory $elementFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\App $application,
        array $data = array()
    ) {
        $this->_elementFactory = $elementFactory;
        parent::__construct($coreData, $context, $application, $data);
    }

    /**
     * Initialise form fields
     */
    protected function _construct()
    {
        $this->addColumn('search', array(
            'label' => __('Search String'),
            'style' => 'width:120px',
        ));
        $this->addColumn('value', array(
            'label' => __('Design Theme'),
            'style' => 'width:120px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add \Exception');
        parent::_construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'value' && isset($this->_columns[$columnName])) {
            /** @var $label \Magento\Core\Model\Theme\Label */
            $label = \Mage::getModel('Magento\Core\Model\Theme\Label');
            $options = $label->getLabelsCollection(__('-- No Theme --'));
            $element = $this->_elementFactory->create('select');
            $element
                ->setForm($this->getForm())
                ->setName($this->_getCellInputElementName($columnName))
                ->setHtmlId($this->_getCellInputElementId('#{_id}', $columnName))
                ->setValues($options);
            return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::renderCellTemplate($columnName);
    }

}
