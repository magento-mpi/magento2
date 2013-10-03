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

namespace Magento\Adminhtml\Block\System\Email\Template\Grid\Renderer;

class Type
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected static $_types = array(
        \Magento\Newsletter\Model\Template::TYPE_HTML    => 'HTML',
        \Magento\Newsletter\Model\Template::TYPE_TEXT    => 'Text',
    );
    public function render(\Magento\Object $row)
    {

        $str = __('Unknown');

        if(isset(self::$_types[$row->getTemplateType()])) {
            $str = self::$_types[$row->getTemplateType()];
        }

        return __($str);
    }
}
