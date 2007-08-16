<?php
/**
 * Tagged products controller
 *
 * @package     Mage
 * @subpackage  Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_ProductController extends Mage_Core_Controller_Front_Action
{
    public function listAction()
    {
        $this->loadLayout();

        Mage::register('tagId', $this->getRequest()->getParam('tagId'));
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('tag/product_result')
        );

        $this->renderLayout();
    }
}