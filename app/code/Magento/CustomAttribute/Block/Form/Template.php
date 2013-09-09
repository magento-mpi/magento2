<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Dynamic attributes Form Block
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomAttribute_Block_Form_Template extends Magento_Core_Block_Abstract
{
    /**
     * Array of attribute renderers data keyed by attribute front-end type
     *
     * @var array
     */
    protected $_renderBlocks    = array();

    /**
     * Add custom renderer block and template for rendering EAV entity attributes
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return Magento_CustomAttribute_Block_Form_Template
     */
    public function addRenderer($type, $block, $template)
    {
        $this->_renderBlocks[$type] = array(
            'block'     => $block,
            'template'  => $template,
        );

        return $this;
    }

    /**
     * Return array of attribute renderers block and template data keyed by front-end type
     *
     * @return array
     */
    public function getRenderers()
    {
        return $this->_renderBlocks;
    }
}
