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
 * Adminhtml system templates grid block type item renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_System_Email_Template_Grid_Renderer_Type
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected static $_types = array(
        Magento_Newsletter_Model_Template::TYPE_HTML    => 'HTML',
        Magento_Newsletter_Model_Template::TYPE_TEXT    => 'Text',
    );
    public function render(Magento_Object $row)
    {

        $str = __('Unknown');

        if(isset(self::$_types[$row->getTemplateType()])) {
            $str = self::$_types[$row->getTemplateType()];
        }

        return __($str);
    }
}
