<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product additional info block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_View_Additional extends Magento_Core_Block_Template
{

    protected $_list;

    protected $_template = 'product/view/additional.phtml';


    public function getChildHtmlList()
    {
        if (is_null($this->_list)) {
            $this->_list = array();
            $layout = $this->getLayout();
            foreach ($this->getChildNames() as $name) {
                $this->_list[] = $layout->renderElement($name);
            }
        }
        return $this->_list;
    }

}
