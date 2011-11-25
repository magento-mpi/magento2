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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Captcha controller
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_CaptchaController extends Mage_Core_Controller_Front_Action
{

    /**
     * Pre dispatch action
     *
     * @return Mage_Core_CaptchaController
     */
    public function preDispatch()
    {
        $isAdmin = $this->getRequest()->getPost('isAdmin');
        if ($isAdmin) {
            // Emulate admin area, otherwise word will be written to the session with different name
            $this->_currentArea = 'adminhtml';
            $this->_sessionNamespace = 'adminhtml';
        }
        return parent::preDispatch();
    }

    /**
     * Refreshes captcha and returns JSON encoded URL to image (AJAX action)
     * Example: {'imgSrc': 'http://example.com/media/captcha/67842gh187612ngf8s.png'}
     *
     * @return null
     */
    public function refreshAction()
    {
        $response = '';
        $request = $this->getRequest();
        $blockType = $request->getPost('blockType');
        $width = $request->getPost('width');
        $height = $request->getPost('height');
        $formId = $request->getPost('formId');
        $isAdmin = $request->getPost('isAdmin');
        if ($blockType && $width && $height) {
            if ($isAdmin) {
                // Start admin environment emulation, because AJAX request came to frontend area and
                // Mage_Core_Helper_Captcha::getConfigNode() will be unable to determine area correctly
                /* @var $emulator Mage_Core_Model_App_Emulation */
                $emulator = Mage::getModel('core/app_emulation');
                $envInfo = $emulator->startEnvironmentEmulation(
                    Mage_Core_Model_App::ADMIN_STORE_ID, Mage_Core_Model_App_Area::AREA_ADMINHTML);
            }
            /* @var $block Mage_Core_Block_Captcha_Zend */
            $block = $this->getLayout()->createBlock($blockType);
            $block->setFormId($formId)
                ->setImgWidth($width)
                ->setImgHeight($height)
                ->setIsAjax(true)
                ->toHtml();
            $response = $block->getImgSrc();
            if ($isAdmin) {
                $emulator->stopEnvironmentEmulation($envInfo);
            }
        }
        $this->getResponse()->setBody(json_encode(array('imgSrc' => $response)));
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
