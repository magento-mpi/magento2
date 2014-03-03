<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pci\Block\Adminhtml\Crypt\Key;

/**
 * Encryption key change edit page block
 *
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Block module name
     *
     * @var string|null
     */
    protected $_blockGroup = null;

    /**
     * Controller name
     *
     * @var string
     */
    protected $_controller = 'crypt_key';

    /**
     * Instantiate save button
     *
     * @return void
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
