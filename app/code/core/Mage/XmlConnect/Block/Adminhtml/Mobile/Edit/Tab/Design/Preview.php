<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tab design preview xml renderer
 *
 * @category     Mage
 * @package      Mage_Xmlconnect
 * @author       Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Preview
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Set preview template
     */
    protected function _construct()
    {
        parent::_construct();

        if (Mage::registry('current_app')) {
            $device = Mage::helper('Mage_XmlConnect_Helper_Data')->getDeviceType();
            if (array_key_exists($device, Mage::helper('Mage_XmlConnect_Helper_Data')->getSupportedDevices())) {
                $template = 'edit/tab/design/preview_' . strtolower($device) . '.phtml';
            } else {
                Mage::throwException(
                    $this->__('Device doesn\'t recognized. Unable to load a template.')
                );
            }

            $this->setTemplate($template);
        }
    }

    /**
     * Retieve preview action url
     *
     * @param string $page
     * @return string
     */
    public function getPreviewActionUrl($page = 'home')
    {
        $params = array();
        $model  = Mage::helper('Mage_XmlConnect_Helper_Data')->getApplication();
        if ($model !== null) {
            if ($model->getId() !== null) {
                $params = array('application_id' => $model->getId());
            } else {
                $params = array('devtype' => $model->getType());
            }
        }
        return $this->getUrl('*/*/preview' . $page, $params);
    }
}
