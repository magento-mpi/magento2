<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Encryption key change edit page block
 *
 */
namespace Magento\Pci\Block\Adminhtml\Crypt\Key;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    protected $_blockGroup = null;
    protected $_controller = 'crypt_key';

    /**
     * Instantiate save button
     *
     */
    protected function _construct()
    {
        \Magento\Object::__construct();
        $this->_addButton('save', array(
            'label'     => __('Change Encryption Key'),
            'class'     => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#edit_form'),
                ),
            ),
        ), 1);
    }

    /**
     * Header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Encryption Key');
    }
}
