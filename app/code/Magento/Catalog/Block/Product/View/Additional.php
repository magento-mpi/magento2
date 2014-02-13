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
namespace Magento\Catalog\Block\Product\View;

class Additional extends \Magento\View\Element\Template
{
    /**
     * @var array
     */
    protected $_list;

    /**
     * @var string
     */
    protected $_template = 'product/view/additional.phtml';


    /**
     * @return array
     */
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
