<?php
/**
 * 
 *
 * @file        IndexController.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Test_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        // Load default layout
        $block = $this->getLayout()->createBlock('tpl', 'upload');
        $block->settemplate('test/index.phtml');
        $this->getResponse()->setBody($block->toHtml());
    }

}
// ft:php
// fileformat:unix
// tabstop:4
?>
