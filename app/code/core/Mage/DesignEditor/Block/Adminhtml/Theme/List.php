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
 * Design editor theme list
 */
class Mage_DesignEditor_Block_Adminhtml_Theme_List extends Mage_Backend_Block_Widget_Container
{
    /**
     * So called "container controller" to specify group of blocks participating in some action
     *
     * @var string
     */
    protected $_controller = 'vde';

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Themes List');
    }
}
