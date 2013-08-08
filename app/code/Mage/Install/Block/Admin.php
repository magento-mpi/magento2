<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Administrator account install block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Block_Admin extends Mage_Install_Block_Abstract
{
    protected $_template = 'create_admin.phtml';

    public function getPostUrl()
    {
        return $this->getUrl('*/*/administratorPost');
    }

    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = Mage::getSingleton('Mage_Install_Model_Session')->getAdminData(true);
            $data = is_array($data) ? $data : array();
            $data = new Magento_Object($data);
            $this->setData('form_data', $data);
        }
        return $data;
    }
}
