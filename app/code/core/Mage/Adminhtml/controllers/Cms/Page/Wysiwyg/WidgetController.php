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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Cms widgets management controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Cms_Page_Wysiwyg_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout('popup');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $config = Mage::getConfig()->loadModulesConfiguration('widget.xml');
        $widgets = $config->getNode('widgets');
        foreach ($widgets->children() as $widget) {
            if ($widget->js) {
                foreach (explode(',', $widget->js) as $js) {
                    $this->getLayout()->getBlock('head')->addJs($js);
                }
            }
        }

        $this->renderLayout();
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function loadOptionsAction()
    {
        try {
            $this->loadLayout('empty');
            $this->renderLayout();
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function buildWidgetAction()
    {
        $code = '{{widget';
        if ($type = $this->getRequest()->getPost('widget_type')) {
            $code .= sprintf(' type="%s"', $type);
        }
        foreach ($this->getRequest()->getPost('parameters') as $name => $value) {
            $code .= sprintf(' %s="%s"', $name, $value);
        }
        $code .= '}}';
        $this->getResponse()->setBody($code);
    }
}
