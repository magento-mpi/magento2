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
 * Adminhtml system templates grid block sender item renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
 
class Magento_Adminhtml_Block_System_Email_Template_Grid_Renderer_Sender extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(\Magento\Object $row)
    {
        $str = '';
        
        if($row->getTemplateSenderName()) {
            $str .= htmlspecialchars($row->getTemplateSenderName()) . ' ';
        }        
        
        if($row->getTemplateSenderEmail()) {
            $str .= '[' . $row->getTemplateSenderEmail() . ']';
        }        
        
        if($str == '') {
            $str .= '---';
        }
            
        return $str;
    }
}
