<?php
    /**
     * {license_notice}
     *
     * @category    Mage
     * @package     Mage_DesignEditor
     * @copyright   {copyright}
     * @license     {license_link}
     */

    /**
     * Design editor exit block
     */
class Mage_DesignEditor_Block_Adminhtml_Exit extends Mage_Adminhtml_Block_Widget
{
    public function _toHtml()
    {
        return '<script type="text/javascript">window.close();</script>';
    }
}
