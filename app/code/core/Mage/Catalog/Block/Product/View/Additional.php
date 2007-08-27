<?php
/**
 * Product additional info block
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Catalog_Block_Product_View_Additional extends Mage_Core_Block_Template
{

    protected $_list;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/view/additional.phtml');
    }

    public function getChildHtmlList()
    {
        if (is_null($this->_list)) {
            $this->_list = array();
            $list = $this->getData('sorted_children_list');
            if (! empty($list)) {
                foreach ($list as $name) {
                    $block = $this->getLayout()->getBlock($name);
                    if (!$block) {
                        Mage::exception('Invalid block: '.$name);
                    }
                    $this->_list[] = $block->toHtml();
                }
            }
        }
        return $this->_list;
    }

}
