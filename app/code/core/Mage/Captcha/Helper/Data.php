<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * @var Mage_Captcha_Model_Session
     */
    protected $_session;

    /**
     * Get Captcha
     *
     * @param string $formId
     * @return Mage_Captcha_Model_Interface
     */
    public function getCaptcha($formId)
    {
        if (!array_key_exists($formId, $this->_captcha)) {
            $type = Mage::helper('captcha')->getConfigNode('type');
            $this->_captcha[$formId] = Mage::getModel('captcha/' . $type, array('formId' => $formId));
        }
        return $this->_captcha[$formId];
    }

    /**
     * Returns session where to save data between page refreshes
     *
     * @param string $formId
     * @return Mage_Captcha_Model_Session
     */
    public function getSession($formId)
    {
        if (!$this->_session) {
            $this->_session = Mage::getSingleton('captcha/session', array('formId' => $formId));
        }
        $this->_session->setFormId($formId);
        return $this->_session;
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
        $area = Mage::app()->getStore()->isAdmin() ? 'admin' : 'customer';
        return Mage::getConfig()->getNode('default/' . $area . '/captcha/' . $id);
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
               $fonts[$fontName] = array('label' => (string)$fontNode->label, 'path' => (string) $fontNode->path);
            }
        }
        return $fonts;
    }
}
