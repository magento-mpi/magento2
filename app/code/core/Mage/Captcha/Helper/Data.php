<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha image model
 *
 * @category   Mage
 * @package    Mage_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Used for "name" attribute of captcha's input field
     */
    const INPUT_NAME_FIELD_VALUE = 'captcha';

    /**
     * Always show captcha
     */
    const MODE_ALWAYS     = 'always';

    /**
     * Show captcha only after certain number of unsuccessful attempts
     */
    const MODE_AFTER_FAIL = 'after_fail';
    const XML_PATH_CAPTCHA_FONTS = 'default/captcha/fonts';

    /**
     * @var array
     */
    protected $_captcha = array();

    /**
     * Get Captcha
     *
     * @param string $formId
     * @return Mage_Captcha_Model_Interface
     */
    public function getCaptcha($formId)
    {
        if (!array_key_exists($formId, $this->_captcha)) {
            $type = $this->getConfigNode('type');
            $this->_captcha[$formId] = Mage::getModel('Mage_Captcha_Model_' . $type, array('formId' => $formId));
        }
        return $this->_captcha[$formId];
    }

    /**
     * Returns value of the node with respect to current area (frontend or backend)
     *
     * @param string $id The last part of XML_PATH_$area_CAPTCHA_ constant (case insensitive)
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfigNode($id)
    {
        return Mage::getStoreConfig((Mage::app()->getStore()->isAdmin() ? 'admin' : 'customer') . '/captcha/' . $id);
    }

    /**
     * Get list of available fonts
     * Return format:
     * [['arial'] => ['label' => 'Arial', 'path' => '/www/magento/fonts/arial.ttf']]
     *
     * @return array
     */
    public function getFonts()
    {
        $node = Mage::getConfig()->getNode(Mage_Captcha_Helper_Data::XML_PATH_CAPTCHA_FONTS);
        $fonts = array();
        if ($node) {
            foreach ($node->children() as $fontName => $fontNode) {
               $fonts[$fontName] = array(
                   'label' => (string)$fontNode->label,
                   'path' => Mage::getBaseDir('base') . DS . $fontNode->path
               );
            }
        }
        return $fonts;
    }
}
