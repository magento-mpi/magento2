<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mage_Backend accordion widget
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Accordion extends Mage_Backend_Block_Widget
{
    protected $_items = array();
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('Mage_Backend::widget/accordion.phtml');
    }
    
    public function getItems()
    {
        return $this->_items;
    }
    
    public function addItem($itemId, $config)
    {
        $this->_items[$itemId] = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Accordion_Item')
            ->setData($config)
            ->setAccordion($this)
            ->setId($itemId);
        if (isset($config['content']) && $config['content'] instanceof Mage_Core_Block_Abstract) {
            $this->_items[$itemId]->setChild($itemId.'_content', $config['content']);
        }
            
        $this->setChild($itemId, $this->_items[$itemId]);
        return $this;
    }
}
