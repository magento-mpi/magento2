<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product additional info block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_View_Additional extends Mage_Core_Block_Template
{

    protected $_list;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('product/view/additional.phtml');
    }

    public function getChildHtmlList()
    {
        if (is_null($this->_list)) {
            $this->_list = array();
            foreach ($this->getSortedChildren() as $name) {
                $this->_list[] = $this->getChildHtml($name);
            }
        }
        return $this->_list;
    }

}
