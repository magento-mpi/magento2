<?php
/**
 * Category form input image element
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Sergiy Lysak <sergey@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Form_Image extends Varien_Data_Form_Element_Abstract
{
    public function __construct($data) 
    {
        parent::__construct($data);
        $this->setType('file');
    }
    
    public function getElementHtml()
    {
        $html = '<table border="0" cellspacing="0" cellpadding="0" width="80%">';
        
        foreach ((array)$this->getValue() as $image) {
            if (!empty($image['image'])) {
                $url = Mage::getSingleton('core/store')->getConfig('web/url/upload').$image['image'];
                $html .= '<tr><td style="padding-bottom:3px;">';
                $html .= '<input type="input" name="'.parent::getName().'[position]['.$image['id'].']" value="'.$image['position'].'" id="'.$this->getHtmlId().'_position_'.$image['id'].'" size="3"/> ';
                $html .= '<a href="'.$url.'" target="_blank" onclick="imagePreview(\''.$this->getHtmlId().'_image_'.$image['id'].'\');return false;"><img
                src="'.$url.'" alt="'.$image['image'].'" width="50" align="absmiddle" class="small-image-preview"></a>
                <div id="'.$this->getHtmlId().'_image_'.$image['id'].'" style="display:none" class="image-preview">
                <img src="'.$url.'" alt="'.$image['image'].'">
                </div>';
                $html .= '<input type="checkbox" name="'.parent::getName().'[delete][]" value="'.$image['id'].'" id="'.$this->getHtmlId().'_delete_'.$image['id'].'"/>';
                $html .= '<label class="normal" for="'.$this->getHtmlId().'_delete_'.$image['id'].'">'.__('Delete Image').'</label>';
                $html .= '</td></tr>';
            }
        }

        $html .= '<tr><td><div id="image_list"></div></td></tr>';

        $html .= '<tr><td><input id="'.$this->getHtmlId().'" name="'.$this->getName()
             .'" value="" '.$this->serialize($this->getHtmlAttributes()).'/></td></tr>'."\n";
             
        $html .= '<script language="javascript">
                    var multi_selector = new MultiSelector( document.getElementById( "image_list" ), "'.$this->getName().'", -1,
                        \'<input type="input" name="'.parent::getName().'[position_new][]" value="" id="'.$this->getHtmlId().'_position_new_%id%" size="3"/> <a href="file:///%file%" target="_blank" onclick="imagePreview(\\\''.$this->getHtmlId().'_image_new_%id%\\\');return false;"><img src="file:///%file%" alt="%file%" width="50" align="absmiddle" class="small-image-preview" style="padding-bottom:3px;"></a> <div id="'.$this->getHtmlId().'_image_new_%id%" style="display:none" class="image-preview"><img src="file:///%file%"></div>\',
                        "'.__('Delete Image').'"
                    );
                    multi_selector.addElement( document.getElementById( "'.$this->getHtmlId().'" ) );
                    </script>';

        $html .= '</table>';
             
        return $html;
    }

    public function getName()
    {
        return  $this->getData('name');
    }
}
