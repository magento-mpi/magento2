<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento_Backend accordion widget
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Widget;

class Accordion extends \Magento\Backend\Block\Widget
{
    protected $_items = array();

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/accordion.phtml';

    public function getItems()
    {
        return $this->_items;
    }
    
    public function addItem($itemId, $config)
    {
        $this->_items[$itemId] = $this->getLayout()
            ->createBlock(
                'Magento\Backend\Block\Widget\Accordion\Item',
                $this->getNameInLayout() . '-' . $itemId
            )
            ->setData($config)
            ->setAccordion($this)
            ->setId($itemId);
        if (isset($config['content']) && $config['content'] instanceof \Magento\Core\Block\AbstractBlock) {
            $this->_items[$itemId]->setChild($itemId.'_content', $config['content']);
        }
            
        $this->setChild($itemId, $this->_items[$itemId]);
        return $this;
    }
}
