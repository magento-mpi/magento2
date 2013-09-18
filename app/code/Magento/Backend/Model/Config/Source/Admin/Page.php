<?php
/**
 * Admin system config sturtup page
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Source\Admin;

class Page implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Menu model
     *
     * @var \Magento\Backend\Model\Menu
     */
    protected $_menu;

    /**
     * @var Magento_Backend_Model_Menu_Filter_IteratorFactory
     *
     * @var \Magento\Core\Model\Config
     */
    protected $_objectFactory;

    /**
     * @var \Magento\Backend\Model\Menu\Filter\IteratorFactory
     */
    protected $_iteratorFactory;

    /**
     * @param \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory
     * @param \Magento\Backend\Model\Menu\Config $menuConfig
     */
    public function __construct(
        \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory,
        \Magento\Backend\Model\Menu\Config $menuConfig
    ) {
        $this->_menu = $menuConfig->getMenu();
        $this->_iteratorFactory = $iteratorFactory;
    }

    public function toOptionArray()
    {
        $options = array();
        $this->_createOptions($options, $this->_menu);
        return $options;
    }

    /**
     * Get menu filter iterator
     *
     * @param \Magento\Backend\Model\Menu $menu menu model
     * @return \Magento\Backend\Model\Menu\Filter\Iterator
     */
    protected function _getMenuIterator(\Magento\Backend\Model\Menu $menu)
    {
        return $this->_iteratorFactory->create(
            array('iterator' => $menu->getIterator())
        );
    }

    /**
     * Create options array
     *
     * @param array $optionArray
     * @param \Magento\Backend\Model\Menu $menu
     * @param int $level
     */
    protected function _createOptions(&$optionArray, \Magento\Backend\Model\Menu $menu, $level = 0)
    {
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        $paddingString = str_repeat($nonEscapableNbspChar, ($level * 4));

        foreach ($this->_getMenuIterator($menu) as $menuItem) {

            /**@var  $menuItem \Magento\Backend\Model\Menu\Item */
            if ($menuItem->getAction()) {
                $optionArray[] = array(
                    'label' =>  $paddingString . $menuItem->getTitle(),
                    'value' => $menuItem->getId(),
                );

                if ($menuItem->hasChildren()) {
                    $this->_createOptions($optionArray, $menuItem->getChildren(), $level + 1);
                }
            } else {
                $children = array();

                if ($menuItem->hasChildren()) {
                    $this->_createOptions($children, $menuItem->getChildren(), $level + 1);
                }

                $optionArray[] = array(
                    'label' => $paddingString . $menuItem->getTitle(),
                    'value' => $children,
                );
            }
        }
    }
}
