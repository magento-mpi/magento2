<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Email
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Block\Adminhtml\Template\Grid\Renderer;

/**
 * Adminhtml system templates grid block sender item renderer
 *
 * @category   Magento
 * @package    Magento_Email
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Sender extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $str = '';
        
        if ($row->getTemplateSenderName()) {
            $str .= htmlspecialchars($row->getTemplateSenderName()) . ' ';
        }        
        
        if ($row->getTemplateSenderEmail()) {
            $str .= '[' . $row->getTemplateSenderEmail() . ']';
        }        
        
        if ($str == '') {
            $str .= '---';
        }
            
        return $str;
    }
}
