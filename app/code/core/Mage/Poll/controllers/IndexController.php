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
        $pollId = 1;
        $pollCollection = Mage::getSingleton('poll_resource/poll_collection');
        #$pollCollection->addPollFilter($pollId);
        $pollCollection->load($pollId);

        print "<pre>debug: \n";
        print_r($pollCollection->getItems());
        print "</pre>\n";
    }

}