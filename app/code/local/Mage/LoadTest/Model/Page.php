<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * LoadTest model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_LoadTest_Model_Page
{
    protected $_urls;
    protected $_gridFields;
    protected $_gridData;

    public function __construct()
    {
        $this->_urls = array(
            'render' => array(
                Mage::getUrl('*/render/categories/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Render Categories'),
                Mage::getUrl('*/render/products/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Render Products'),
                Mage::getUrl('*/render/customers/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Render Customers'),
                Mage::getUrl('*/render/reviews/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Render Reviews and Ratings'),
                Mage::getUrl('*/render/tags/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Render Tags'),
                Mage::getUrl('*/render/quotes/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Render Quotes'),
                Mage::getUrl('*/render/orders/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Render Orders')
            ),
            'delete' => array(
                Mage::getUrl('*/delete/categories/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Delete All Categories'),
                Mage::getUrl('*/delete/products/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Delete All Products'),
                Mage::getUrl('*/delete/customers/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Delete All Customers'),
                Mage::getUrl('*/delete/reviews/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Delete All Reviews and Ratings'),
                Mage::getUrl('*/delete/tags/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Delete All Tags'),
                Mage::getUrl('*/delete/quotes/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Delete All Quotes'),
                Mage::getUrl('*/delete/orders/') => Mage::helper('Mage_LoadTest_Helper_Data')->__('Delete All Orders')
            )
        );
    }

    public function pageIndex()
    {
        print '<p><b>Select LoadTest render module</b></p>';
        print '<ul>';
        foreach ($this->_urls['render'] as $url => $text) {
            print '<li><a href="'.$url.'">'.$text.'</a></li>';
        }
        print '</ul>';
        print '<p><b>Select LoadTest delete module</b></p>';
        print '<ul>';
        foreach ($this->_urls['delete'] as $url => $text) {
            print '<li><a href="'.$url.'">'.$text.'</a></li>';
        }
        print '</ul>';
    }

    public function pageStat($rendererObject, $method = 'render')
    {
        $this->_gridFields  = array();
        $this->_gridData    = array();

        $gridCols = array();
        $gridCols[] = array(
            'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('ID'),
            'field' => 'key'
        );
        $operationName = $method == 'render'
            ? Mage::helper('Mage_LoadTest_Helper_Data')->__('Render')
            : Mage::helper('Mage_LoadTest_Helper_Data')->__('Delete');
        $recursion = false;
        $rendererField = null;
        $rendererAdditional = array();

        switch (get_class($rendererObject)) {
            case 'Mage_LoadTest_Model_Renderer_Catalog':
                if ($rendererObject->getType() == 'PRODUCT') {
                    $rendererField = 'products';
                    $gridCols[] = array(
                        'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Product Name'),
                        'field' => 'index'
                    );
                }
                else {
                    if ($method == 'render') {
                        $rendererField = 'categories';
                        $recursion = 1;
                        $rendererAdditional[] = array(
                            Mage::helper('Mage_LoadTest_Helper_Data')->__('Update categories tree and URLs')
                        );
                    }
                    else {
                        $recursion = 2;
                        $rendererField = 'categories';
                    }

                    $gridCols[] = array(
                        'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Category Name'),
                        'field' => 'index'
                    );
                }
                break;

            case 'Mage_LoadTest_Model_Renderer_Customer':
                $rendererField = 'customers';
                $gridCols[] = array(
                    'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Customer First Name'),
                    'field' => 'firstname'
                );
                $gridCols[] = array(
                    'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Customer Last Name'),
                    'field' => 'lastname'
                );
                $gridCols[] = array(
                    'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Customer Email'),
                    'field' => 'email'
                );
                break;

            case 'Mage_LoadTest_Model_Renderer_Review':
                $rendererField = 'reviews';
                $gridCols[] = array(
                    'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Review Title'),
                    'field' => 'review_title'
                );
                $gridCols[] = array(
                    'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Customer Name'),
                    'field' => 'customer_name'
                );
                $gridCols[] = array(
                    'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Product Name'),
                    'field' => 'product_name'
                );
                break;

            case 'Mage_LoadTest_Model_Renderer_Tag':
                $rendererField = 'tags';
                $gridCols[] = array(
                    'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Tag Name'),
                    'field' => 'index'
                );
                break;

            case 'Mage_LoadTest_Model_Renderer_Sales':
                if ($rendererObject->getType() == 'ORDER') {
                    $rendererField = 'orders';
                    $gridCols[] = array(
                        'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Customer ID'),
                        'field' => 'customer_id'
                    );
                    $gridCols[] = array(
                        'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Customer Name'),
                        'field' => 'customer_name'
                    );
                }
                else {
                    $rendererField = 'quotes';
                    $gridCols[] = array(
                        'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Customer ID'),
                        'field' => 'customer_id'
                    );
                    $gridCols[] = array(
                        'name'  => Mage::helper('Mage_LoadTest_Helper_Data')->__('Customer Name'),
                        'field' => 'customer_name'
                    );
                }
        }

        foreach ($gridCols as $gridProp) {
            $this->_gridFields[] = $gridProp['name'];
        }
        $this->_gridFields[] = Mage::helper('Mage_LoadTest_Helper_Data')->__('Memory After, MB');
        $this->_gridFields[] = Mage::helper('Mage_LoadTest_Helper_Data')->__('Memory Before, MB');
        $this->_gridFields[] = Mage::helper('Mage_LoadTest_Helper_Data')->__('Execution, sec');

        $tableProps = array(
            'grid'      => $gridCols,
            'memory'    => $rendererObject->getUsedMemory(),
            'number'    => 0
        );

        $tableProps = $this->_tableData(
            $rendererObject->$rendererField,
            $tableProps,
            $recursion
        );
        $i = $tableProps['number'];

        if ($rendererAdditional) {
            $memory = $rendererObject->getUsedMemory();
            foreach ($rendererAdditional as $additional) {
                $i++;
                $gridRow = array();
                $gridRow[] = 0;
                foreach ($additional as $v) {
                    $gridRow[] = $v;
                }
                $gridRow[] = sprintf('%.3f', $memory[$i]['before'] / 1024 / 1024);
                $gridRow[] = sprintf('%.3f', $memory[$i]['after'] / 1024 / 1024);
                $gridRow[] = sprintf('%.3f', $memory[$i]['after_time'] - $memory[$i]['before_time']);
                $this->_gridData[] = $gridRow;
            }
        }

        $this->_renderTable();
    }

    public function exception($message)
    {
        print '<p><b>Error:</b> ' . $message . '</p>';
        print '<p><a href="'.Mage::getUrl('*/index/').'">'.Mage::helper('Mage_LoadTest_Helper_Data')->__('Back to index').'</a></p>';
        die();
    }

    protected function _tableData($dataArray, $tableProps, $recursion = false, $index = 0)
    {
        $gridCols   = $tableProps['grid'];
        $memory     = $tableProps['memory'];
        $i          = $tableProps['number'];

        if ($recursion) {
            $data = isset($dataArray[$index]) ? $dataArray[$index] : null;
        } else {
            $data = $dataArray;
        }

        if (!is_array($data)) {
            return $tableProps;
        }

        foreach ($data as $key => $fieldProp) {
            $i ++;
            $gridRow = array();
            foreach ($gridCols as $gridProp) {
                if ($gridProp['field'] == 'key') {
                    $gridRow[] = $key;
                }
                elseif ($gridProp['field'] == 'index') {
                    $gridRow[] = $fieldProp;
                }
                else {
                    $gridRow[] = $fieldProp[$gridProp['field']];
                }
            }
            if (isset($memory[$i])) {
                $gridRow[] = sprintf('%.3f', $memory[$i]['before'] / 1024 / 1024);
                $gridRow[] = sprintf('%.3f', $memory[$i]['after'] / 1024 / 1024);
                $gridRow[] = sprintf('%.3f', $memory[$i]['after_time'] - $memory[$i]['before_time']);
            }
            else {
                $gridRow[] = Mage::helper('Mage_LoadTest_Helper_Data')->__('Unknown');
                $gridRow[] = Mage::helper('Mage_LoadTest_Helper_Data')->__('Unknown');
                $gridRow[] = Mage::helper('Mage_LoadTest_Helper_Data')->__('Unknown');
            }

            $this->_gridData[] = $gridRow;

            if ($recursion == 1 && isset($dataArray[$key])) {
                $tableProps['number'] = $i;
                $tableProps = $this->_tableData($dataArray, $tableProps, $recursion, $key);
                $i = $tableProps['number'];
            }
        }
        $tableProps['number'] = $i;
        return $tableProps;
    }

    protected function _renderTable($title = null)
    {
        print '<table border=1>' . "\n";
        if ($title) {
            print '<caption>'.$title.'</caption>';
        }
        print '<tr><td>' . join('</td><td>', $this->_gridFields) . '</td></tr>' . "\n";
        foreach ($this->_gridData as $row) {
            print '<tr><td>' . join('</td><td>', $row) . '</td></tr>' . "\n";
        }
        print '</table>';

        print '<p><a href="'.Mage::getUrl('*/index/').'">'.Mage::helper('Mage_LoadTest_Helper_Data')->__('Back to index').'</a></p>';
    }
}