<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml system templates grid block type item renderer
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Adminhtml_Template_Grid_Renderer_Type extends
    Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders row type value
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        $str = 'Unknown';
        $types = Mage::getSingleton('Saas_PrintedTemplate_Model_Source_Type')->getAllOptions();
        if (isset($types[$row->getEntityType()])) {
            $str = $types[$row->getEntityType()];
        }

        return $this->__($str);
    }
}
