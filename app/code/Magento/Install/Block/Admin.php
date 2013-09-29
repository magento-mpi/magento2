<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Administrator account install block
 */
class Magento_Install_Block_Admin extends Magento_Install_Block_Abstract
{
    /**
     * @var string
     */
    protected $_template = 'create_admin.phtml';

    /**
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('*/*/administratorPost');
    }

    /**
     * @return Magento_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (null === $data) {
            $data = $this->_session->getAdminData(true);
            $data = is_array($data) ? $data : array();
            $data = new Magento_Object($data);
            $this->setData('form_data', $data);
        }
        return $data;
    }
}
