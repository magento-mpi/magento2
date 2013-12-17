<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Email
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml system templates grid block type item renderer
 *
 * @category   Magento
 * @package    Magento_Email
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Email\Block\Adminhtml\Template\Grid\Renderer;

class Type extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected static $_types = array(
        \Magento\Email\Model\Template::TYPE_HTML => 'HTML',
        \Magento\Email\Model\Template::TYPE_TEXT => 'Text',
    );

    public function render(\Magento\Object $row)
    {

        $str = __('Unknown');

        if (isset(self::$_types[$row->getTemplateType()])) {
            $str = self::$_types[$row->getTemplateType()];
        }

        return __($str);
    }
}
