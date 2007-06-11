<?php
/**
 * Poll index controller
 *
 * @file        IndexController.php
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski (hacki) alexander@varien.com
 */

class Mage_Poll_IndexController extends Mage_Core_Controller_Front_Action
{

    public function IndexAction()
    {
        $this->loadLayout();

        $pollModel = Mage::getModel('poll/poll');

        $website_id = Mage::registry('website')->getId();

        $pollModel->setWebsiteId($website_id);
        $pollModel->loadPolls();

        print "<pre>debug: \n";
        print_r($pollModel);
        print "</pre>\n";
         

        $block = $this->getLayout()->createBlock('tpl', 'poll.block');
        $block->setTemplate('poll/poll_list.phtml');
        $this->getLayout()->getBlock('content')->append($block);

        $this->renderLayout();
    }

}
 
// ft:php
// fileformat:unix
// tabstop:4
?>
