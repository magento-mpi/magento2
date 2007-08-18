<?php
/**
 * Adminhtml products tagged by tag
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tag_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        parent::__construct();

        switch( $this->getRequest()->getParam('ret') ) {
            case 'all':
                $url = Mage::getUrl('*/*/');
                break;

            case 'pending':
                $url = Mage::getUrl('*/*/pending');
                break;

            default:
                $url = Mage::getUrl('*/*/');
        }


        $this->_block = 'tag_product';
        $this->_controller = 'tag_product';
        $this->_removeButton('add');
        $this->setBackUrl($url);
        $this->_addBackButton();

        $tagInfo = Mage::getModel('tag/tag')
            ->load(Mage::registry('tagId'));

        $this->_headerText = __("Products Tagged With '{$tagInfo->getName()}'");
    }

}
