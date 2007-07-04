<?php
/**
 * Adminhtml newsletter template preview block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Mage_Adminhtml_Block_Newsletter_Template_Preview extends Mage_Adminhtml_Block_Widget 
{
    public function toHtml() 
    {
        $template = Mage::getModel('newsletter/template');
        if($id = (int)$this->getRequest()->getParam('id')) {
            $template->load($id);
        } else { 
            
            $template->setTemplateType($this->getRequest()->getParam('type'));
            $template->setTemplateText($this->getRequest()->getParam('text'));
            
            
        }
        
        
        $templateProcessed = $template->getProcessedTemplate();
        if($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }
        
        return $templateProcessed;
    }
}