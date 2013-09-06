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
 * Admin system config sturtup page
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * Default construct
     */
    public function __construct(array $data = array())
    {
        $this->_menu = isset($data['menu']) ?
            $data['menu'] :
            Mage::getSingleton('Magento_Backend_Model_Menu_Config')->getMenu();

        $this->_objectFactory = isset($data['objectFactory']) ? $data['objectFactory'] : $this->_objectFactory;
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
        return $this->_objectFactory->getModelInstance('Magento_Backend_Model_Menu_Filter_Iterator',
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
