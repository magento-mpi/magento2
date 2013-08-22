<?php
/**
 * Admin system config sturtup page
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Source_Admin_Page implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Menu model
     *
     * @var Mage_Backend_Model_Menu
     */
    protected $_menu;

    /**
     * Object factory
     *
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * @var Mage_Backend_Model_Menu_Filter_IteratorFactory
     */
    protected $_iteratorFactory;

    /**
     * @param Mage_Backend_Model_Menu_Filter_IteratorFactory $iteratorFactory
     * @param Mage_Backend_Model_Menu_Config $menuConfig
     */
    public function __construct(
        Mage_Backend_Model_Menu_Filter_IteratorFactory $iteratorFactory,
        Mage_Backend_Model_Menu_Config $menuConfig
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
     * @param Mage_Backend_Model_Menu $menu menu model
     * @return Mage_Backend_Model_Menu_Filter_Iterator
     */
    protected function _getMenuIterator(Mage_Backend_Model_Menu $menu)
    {
        return $this->_iteratorFactory->create(
            array('iterator' => $menu->getIterator())
        );
    }

    /**
     * Create options array
     *
     * @param array $optionArray
     * @param Mage_Backend_Model_Menu $menu
     * @param int $level
     */
    protected function _createOptions(&$optionArray, Mage_Backend_Model_Menu $menu, $level = 0)
    {
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        $paddingString = str_repeat($nonEscapableNbspChar, ($level * 4));

        foreach ($this->_getMenuIterator($menu) as $menuItem) {

            /**@var  $menuItem Mage_Backend_Model_Menu_Item */
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
