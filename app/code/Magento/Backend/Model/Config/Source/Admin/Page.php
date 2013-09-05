<?php
/**
 * Admin system config sturtup page
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Source_Admin_Page implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Menu model
     *
     * @var Magento_Backend_Model_Menu
     */
    protected $_menu;

    /**
     * Object factory
     *
     * @var Magento_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * @var Magento_Backend_Model_Menu_Filter_IteratorFactory
     */
    protected $_iteratorFactory;

    /**
     * @param Magento_Backend_Model_Menu_Filter_IteratorFactory $iteratorFactory
     * @param Magento_Backend_Model_Menu_Config $menuConfig
     */
    public function __construct(
        Magento_Backend_Model_Menu_Filter_IteratorFactory $iteratorFactory,
        Magento_Backend_Model_Menu_Config $menuConfig
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
     * @param Magento_Backend_Model_Menu $menu menu model
     * @return Magento_Backend_Model_Menu_Filter_Iterator
     */
    protected function _getMenuIterator(Magento_Backend_Model_Menu $menu)
    {
        return $this->_iteratorFactory->create(
            array('iterator' => $menu->getIterator())
        );
    }

    /**
     * Create options array
     *
     * @param array $optionArray
     * @param Magento_Backend_Model_Menu $menu
     * @param int $level
     */
    protected function _createOptions(&$optionArray, Magento_Backend_Model_Menu $menu, $level = 0)
    {
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        $paddingString = str_repeat($nonEscapableNbspChar, ($level * 4));

        foreach ($this->_getMenuIterator($menu) as $menuItem) {

            /**@var  $menuItem Magento_Backend_Model_Menu_Item */
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
