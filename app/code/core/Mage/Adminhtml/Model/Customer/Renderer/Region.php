<?php
/**
 * REgion field renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Model_Customer_Renderer_Region implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Country region collections
     * 
     * array(
     *      [$countryId] => Varien_Data_Collection_Db
     * )
     * 
     * @var array
     */
    static protected $_regionCollections;
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<span class="field-row">'."\n";
        
        $countryId = false;
        if ($country = $element->getForm()->getElement('country_id')) {
            $countryId = $country->getValue();
        }
        
        $regionCollection = false;
        if ($countryId) {
            if (!isset(self::$_regionCollections[$countryId])) {
                self::$_regionCollections[$countryId] = Mage::getModel('directory/country')
                    ->setId($countryId)
                    ->getLoadedRegionCollection();
            }
            $regionCollection = self::$_regionCollections[$countryId];
        }
        
        $regionId = $element->getForm()->getElement('region_id')->getValue();

        if ($regionCollection && $regionCollection->getSize()) {
            $html.= $element->getLabelHtml();
            $html.= '<select id="'.$element->getHtmlId().'" name="'.$element->getName().'" '
                 .$element->serialize($element->getHtmlAttributes()).'>'."\n";
            foreach ($regionCollection as $region) {
                $selected = ($regionId==$region->getId()) ? ' selected' : '';
            	$html.= '<option value="'.$region->getId().'"'.$selected.'>'.$region->getName().'</option>';
            }
            $html.= '</select>';
        }
        else {
            $element->setClass('input-text');
            $element->setRequired(false);
            
            $html.= $element->getLabelHtml();
            $html.= '<input id="'.$element->getHtmlId().'" name="'.$element->getName()
                 .'" value="'.$element->getEscapedValue().'"'.$element->serialize($element->getHtmlAttributes()).'/>'."\n";
        }
        $html.= '</span>'."\n";
        /*$html.= '<input id="'.$this->getHtmlId().'" name="'.$this->getName()
             .'" value="'.$this->getEscapedValue().'"'.$this->serialize($this->getHtmlAttributes()).'/>'."\n";
        $html.= '</span>'."\n";*/
        return $html;
    }
}
