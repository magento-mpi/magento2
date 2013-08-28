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
 * Customer address region field renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Edit_Renderer_Region
    extends Magento_Backend_Block_Abstract
    implements Magento_Data_Form_Element_Renderer_Interface
{
    /**
     * Directory data
     *
     * @var Magento_Directory_Helper_Data
     */
    protected $_directoryData = null;

    /**
     * @param Magento_Directory_Helper_Data $directoryData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Directory_Helper_Data $directoryData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Context $context,
        array $data = array()
    ) {
        $this->_directoryData = $directoryData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Output the region element and javasctipt that makes it dependent from country element
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        if ($country = $element->getForm()->getElement('country_id')) {
            $countryId = $country->getValue();
        }
        else {
            return $element->getDefaultHtml();
        }

        $regionId = $element->getForm()->getElement('region_id')->getValue();

        $html = '<div class="field field-state required">';
        $element->setClass('input-text');
        $element->setRequired(true);
        $html .=  $element->getLabelHtml() . '<div class="control">';
        $html .= $element->getElementHtml();

        $selectName = str_replace('region', 'region_id', $element->getName());
        $selectId = $element->getHtmlId() . '_id';
        $html .= '<select id="' . $selectId . '" name="' . $selectName
            . '" class="select required-entry" style="display:none">';
        $html .= '<option value="">' . __('Please select') . '</option>';
        $html .= '</select>';

        $html .= '<script type="text/javascript">' . "\n";
        $html .= '$("' . $selectId . '").setAttribute("defaultValue", "' . $regionId.'");' . "\n";
        $html .= 'new regionUpdater("' . $country->getHtmlId() . '", "' . $element->getHtmlId()
            . '", "' . $selectId . '", ' . $this->_directoryData->getRegionJson() . ');' . "\n";
        $html .= '</script>' . "\n";

        $html .= '</div></div>' . "\n";

        return $html;
    }
}
