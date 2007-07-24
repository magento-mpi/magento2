<?php
/**
 * Adminhtml system template preview block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Mage_Adminhtml_Block_System_Email_Template_Preview extends Mage_Adminhtml_Block_Widget 
{
    public function toHtml() 
    {
        $template = Mage::getModel('core/email_template');
        if($id = (int)$this->getRequest()->getParam('id')) {
            $template->load($id);
        } else { 
            $template->setTemplateType($this->getRequest()->getParam('type'));
            $template->setTemplateText($this->getRequest()->getParam('text'));    
        }
        
        Varien_Profiler::start("email_template_proccessing");
        $vars = array();
        
        if($this->getRequest()->getParam('subscriber')) {
        	$vars['subscriber'] = Mage::getModel('newsletter/subscriber')
        		->load($this->getRequest()->getParam('subscriber'));
        }
        
        $templateProcessed = $template->getProcessedTemplate($vars, true);
        
        if($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }
        
        Varien_Profiler::stop("email_template_proccessing");
        
        return $templateProcessed;
    }
}