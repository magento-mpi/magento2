<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * user roles block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Api_Roles extends Mage_Adminhtml_Block_Template
{

    protected $_template = 'api/roles.phtml';

    public function getAddNewUrl()
    {
        return $this->getUrl('*/*/editrole');
    }

    public function getGridHtml()
    {
        return $this->getLayout()->createBlock('Mage_Adminhtml_Block_Api_Grid_Role')->toHtml();
    }
}
