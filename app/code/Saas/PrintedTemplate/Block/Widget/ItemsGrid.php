<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget to display grid with items
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_ItemsGrid extends Saas_PrintedTemplate_Block_Widget_AbstractGrid
{
    /**
     * List of fields that contain price information
     *
     * @var array
     */
    protected $_priceFields = array(
        'price', 'tax_amount', 'weee_tax_applied_row_amount', 'discount_amount', 'price_incl_tax', 'row_total',
        'discount', 'discount_rate', 'price_incl_discount', 'row_total_incl_discount', 'tax_rates', 'row_total_inc',
        'discount_excl_tax', 'row_total_incl_discount_excl_tax', 'price_incl_discount_excl_tax',
        'row_total_incl_discount_and_tax',
    );

    /**
     * Cache for getColumns() method
     *
     * @var Magento_Data_Collection
     */
    protected $_columns;

    /**
     * Cache for getItems() method
     *
     * @var Magento_Data_Collection
     */
    protected $_items;

    /**
     * Row renderers class names
     *
     * @var array Array with strings
     */
    protected $_renderers = array(
        'default' => 'Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default',
        'bundle'  => 'Saas_PrintedTemplate_Block_Widget_Item_Renderer_Bundle'
    );

    /**
     * Initializes object
     *
     * @see Magento_Core_Block_Template::_construct()
     */
    protected function _construct()
    {
        $this->setTemplate('Saas_PrintedTemplate::widget/items_grid.phtml');
        $this->_styleMap['header'] = array(
            'font-family' => array('header_font_family', 'font_family'),
            'font-size'   => array('header_font_size',   'size_pt'),
            'font-style'  => array('header_font_italic', 'font_style'),
            'font-weight' => array('header_font_bold',   'font_weight'),
        );
        $this->_styleMap['item'] = array(
            'font-family' => array('row_font_family', 'font_family'),
            'font-size'   => array('row_font_size',   'size_pt'),
            'font-style'  => array('row_font_italic', 'font_style'),
            'font-weight' => array('row_font_bold',   'font_weight'),
        );
    }

    /**
     * Check if property contains price information
     *
     * @param string $property
     * @return boolean
     */
    public function isPriceProperty($property)
    {
        return in_array($property, $this->_priceFields);
    }

    /**
     * Get columns collection
     *
     * @return Magento_Data_Collection
     */
    public function getColumns()
    {
        if (is_null($this->_columns)) {
            $this->_columns = new Magento_Data_Collection();

            if ($this->hasColumnsEditor()) {
                parse_str(base64_decode($this->getColumnsEditor()));

                if (isset($parameters['columns_editor']) && is_array($parameters['columns_editor'])) {
                    // Check if we have any sort order by summing all sort_orders
                    if (array_reduce($parameters['columns_editor'], array($this, '_reduceColumns'))) {
                        usort($parameters['columns_editor'], array($this, '_compareColumns'));
                    }
                    foreach ($parameters['columns_editor'] as $row) {
                        $this->_columns->addItem(new Magento_Object($row));
                    }
                }
            }
        }

        return $this->_columns;
    }

    /**
     * Callback function for sorting columns
     *
     * @param array $firstColumn Array with sort_order key
     * @param array $secondColumn Array with sort_order key
     * @return boolean Compare by sort_order and returns either 1 or 0 or -1 ($a > $b, $a = $b, $a < $b)
     */
    protected function _compareColumns(array $firstColumn, array $secondColumn)
    {
        if (!isset($firstColumn['sort_order'], $secondColumn['sort_order'])) {
            return 0;
        }
        if ($firstColumn['sort_order'] > $secondColumn['sort_order']) {
            return 1;
        }
        if ($firstColumn['sort_order'] < $secondColumn['sort_order']) {
            return -1;
        } else {
            return 0;
        }
    }

    /**
     * Call back function for array_reduce; sum all argurents by absolute value of sort_order
     *
     * @param int $sum
     * @param array $column
     * @return int
     */
    protected function _reduceColumns($sum, array $column)
    {
        return isset($column['sort_order'])
            ? $sum + abs($column['sort_order'])
            : $sum;
    }

    /**
     * Get items of entity if has entity
     *
     * @return Magento_Data_Collection|array Collection or empty array
     */
    public function getItems()
    {
        if (is_null($this->_items)) {
            $this->_items = ($this->hasEntity()) ? $this->getEntity()->getItems() : array();
        }

        return $this->_items;
    }

    /**
     * Render item
     *
     * @param  $item
     * @return string
     */
    public function renderItem($item)
    {
        $productType = $item->getOrderItem()->getProductType();
        return $this->_getRenderer($productType)
            ->setItemsGridBlock($this)
            ->setItem($item)
            ->setColumns($this->getColumns())
            ->toHtml();
    }

    /**
     * Get item renderer
     *
     * @param string $type
     * @return Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default
     */
    protected function _getRenderer($type)
    {
        if (!isset($this->_renderers[$type])) {
            $type = 'default';
        }

        return $this->getLayout()->createBlock($this->_renderers[$type]);
    }
}
