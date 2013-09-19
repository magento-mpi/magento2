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
 * Fieldset config form element renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Frontend_Product_Watermark
    extends Magento_Backend_Block_Abstract
    implements Magento_Data_Form_Element_Renderer_Interface
{
    const XML_PATH_IMAGE_TYPES = 'global/catalog/product/media/image_types';

    /**
     * @var Magento_Data_Form_Element_Factory
     */
    protected $_elementFactory;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @param Magento_Data_Form_Element_Factory $elementFactory
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Element_Factory $elementFactory,
        Magento_Backend_Block_Context $context,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_coreConfig = $coreConfig;
        parent::__construct($context, $data);
    }

    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        $renderer = Mage::getBlockSingleton('Magento_Backend_Block_System_Config_Form_Field');

        $attributes = $this->_coreConfig->getNode(self::XML_PATH_IMAGE_TYPES)->asArray();

        foreach ($attributes as $key => $attribute) {
            /**
             * Watermark size field
             */
            /** @var Magento_Data_Form_Element_Text $field */
            $field = $this->_elementFactory->create('text');
            $field->setName("groups[watermark][fields][{$key}_size][value]")
                ->setForm($this->getForm())
                ->setLabel(__('Size for %1', $attribute['title']))
                ->setRenderer($renderer);
            $html.= $field->toHtml();

            /**
             * Watermark upload field
             */
            /** @var Magento_Data_Form_Element_Imagefile $field */
            $field = $this->_elementFactory->create('imagefile');
            $field->setName("groups[watermark][fields][{$key}_image][value]")
                ->setForm($this->getForm())
                ->setLabel(__('Watermark File for %1', $attribute['title']))
                ->setRenderer($renderer);
            $html.= $field->toHtml();

            /**
             * Watermark position field
             */
            /** @var Magento_Data_Form_Element_Select $field */
            $field = $this->_elementFactory->create('select');
            $field->setName("groups[watermark][fields][{$key}_position][value]")
                ->setForm($this->getForm())
                ->setLabel(__('Position of Watermark for %1', $attribute['title']))
                ->setRenderer($renderer)
                ->setValues(Mage::getSingleton('Magento_Catalog_Model_Config_Source_Watermark_Position')->toOptionArray());
            $html.= $field->toHtml();
        }

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getHeaderHtml($element)
    {
        $id = $element->getHtmlId();
        $default = !$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store');

        $html = '<h4 class="icon-head head-edit-form">'.$element->getLegend().'</h4>';
        $html.= '<fieldset class="config" id="'.$element->getHtmlId().'">';
        $html.= '<legend>'.$element->getLegend().'</legend>';

        // field label column
        $html.= '<table cellspacing="0"><colgroup class="label" /><colgroup class="value" />';
        if (!$default) {
            $html.= '<colgroup class="use-default" />';
        }
        $html.= '<tbody>';

        return $html;
    }

    protected function _getFooterHtml($element)
    {
        $html = '</tbody></table></fieldset>';
        return $html;
    }
}
