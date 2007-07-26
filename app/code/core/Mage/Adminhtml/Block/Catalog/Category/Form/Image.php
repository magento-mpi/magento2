<?php
/**
 * Category form input image element
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
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
        $html = parent::getElementHtml();
        if ($this->getValue()) {
            // need web/url/upload !!!
            $url = Mage::getSingleton('core/store')->getConfig('system/filesystem/upload').$this->getValue();
            $html.= '(<a href="'.$url.'">'.$this->getValue().'</a>)';
        }
        return $html;
    }

    
    public function getName()
    {
        return  $this->getData('name');
    }
}
