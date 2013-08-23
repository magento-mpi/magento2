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
 * Adminhtml form key content block
 */
class Magento_Adminhtml_Block_Admin_Formkey extends Magento_Backend_Block_Template
{
    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->_session->getFormKey();
    }
}
