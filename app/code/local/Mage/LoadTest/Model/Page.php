<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
                Mage::getUrl('*/render/categories/') => Mage::helper('loadtest')->__('Render Categories'),
                Mage::getUrl('*/render/products/') => Mage::helper('loadtest')->__('Render Products'),
                Mage::getUrl('*/render/customers/') => Mage::helper('loadtest')->__('Render Customers'),
                Mage::getUrl('*/render/reviews/') => Mage::helper('loadtest')->__('Render Reviews and Ratings'),
                Mage::getUrl('*/render/tags/') => Mage::helper('loadtest')->__('Render Tags'),
                Mage::getUrl('*/render/quotes/') => Mage::helper('loadtest')->__('Render Quotes'),
                Mage::getUrl('*/render/orders/') => Mage::helper('loadtest')->__('Render Orders')
            ),
            'delete' => array(
                Mage::getUrl('*/delete/categories/') => Mage::helper('loadtest')->__('Delete All Categories'),
                Mage::getUrl('*/delete/products/') => Mage::helper('loadtest')->__('Delete All Products'),
                Mage::getUrl('*/delete/customers/') => Mage::helper('loadtest')->__('Delete All Customers'),
                Mage::getUrl('*/delete/reviews/') => Mage::helper('loadtest')->__('Delete All Reviews and Ratings'),
                Mage::getUrl('*/delete/tags/') => Mage::helper('loadtest')->__('Delete All Tags'),
                Mage::getUrl('*/delete/quotes/') => Mage::helper('loadtest')->__('Delete All Quotes'),
                Mage::getUrl('*/delete/orders/') => Mage::helper('loadtest')->__('Delete All Orders')
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
            'name'  => Mage::helper('loadtest')->__('ID'),
            'field' => 'key'
        );
        $operationName = $method == 'render'
            ? Mage::helper('loadtest')->__('Render')
            : Mage::helper('loadtest')->__('Delete');
        $recursion = false;
        $rendererField = null;
        $rendererAdditional = array();

        switch (get_class($rendererObject)) {
            case 'Mage_LoadTest_Model_Renderer_Catalog':
                if ($rendererObject->getType() == 'PRODUCT') {
                    $rendererField = 'products';
                    $gridCols[] = array(
                        'name'  => Mage::helper('loadtest')->__('Product Name'),
                        'field' => 'index'
                    );
                }
                else {
                    if ($method == 'render') {
                        $rendererField = 'categories';
                        $recursion = 1;
                        $rendererAdditional[] = array(
                            Mage::helper('loadtest')->__('Update categories tree and URLs')
                        );
                    }
                    else {
                        $recursion = 2;
                        $rendererField = 'categories';
                    }

                    $gridCols[] = array(
                        'name'  => Mage::helper('loadtest')->__('Category Name'),
                        'field' => 'index'
                    );
                }
                break;

            case 'Mage_LoadTest_Model_Renderer_Customer':
                $rendererField = 'customers';
                $gridCols[] = array(
                    'name'  => Mage::helper('loadtest')->__('Customer First Name'),
                    'field' => 'firstname'
                );
                $gridCols[] = array(
                    'name'  => Mage::helper('loadtest')->__('Customer Last Name'),
                    'field' => 'lastname'
                );
                $gridCols[] = array(
                    'name'  => Mage::helper('loadtest')->__('Customer Email'),
                    'field' => 'email'
                );
                break;

            case 'Mage_LoadTest_Model_Renderer_Review':
                $rendererField = 'reviews';
                $gridCols[] = array(
                    'name'  => Mage::helper('loadtest')->__('Review Title'),
                    'field' => 'review_title'
                );
                $gridCols[] = array(
                    'name'  => Mage::helper('loadtest')->__('Customer Name'),
                    'field' => 'customer_name'
                );
                $gridCols[] = array(
                    'name'  => Mage::helper('loadtest')->__('Product Name'),
                    'field' => 'product_name'
                );
                break;

            case 'Mage_LoadTest_Model_Renderer_Tag':
                $rendererField = 'tags';
                $gridCols[] = array(
                    'name'  => Mage::helper('loadtest')->__('Tag Name'),
                    'field' => 'index'
                );
                break;

            case 'Mage_LoadTest_Model_Renderer_Sales':
                if ($rendererObject->getType() == 'ORDER') {
                    $rendererField = 'orders';
                    $gridCols[] = array(
                        'name'  => Mage::helper('loadtest')->__('Customer ID'),
                        'field' => 'customer_id'
                    );
                    $gridCols[] = array(
                        'name'  => Mage::helper('loadtest')->__('Customer Name'),
                        'field' => 'customer_name'
                    );
                }
                else {
                    $rendererField = 'quotes';
                    $gridCols[] = array(
                        'name'  => Mage::helper('loadtest')->__('Customer ID'),
                        'field' => 'customer_id'
                    );
                    $gridCols[] = array(
                        'name'  => Mage::helper('loadtest')->__('Customer Name'),
                        'field' => 'customer_name'
                    );
                }
        }

        foreach ($gridCols as $gridProp) {
            $this->_gridFields[] = $gridProp['name'];
        }
        $this->_gridFields[] = Mage::helper('loadtest')->__('Memory After, MB');
        $this->_gridFields[] = Mage::helper('loadtest')->__('Memory Before, MB');
        $this->_gridFields[] = Mage::helper('loadtest')->__('Execution, sec');

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
        print '<p><a href="'.Mage::getUrl('*/index/').'">'.Mage::helper('loadtest')->__('Back to index').'</a></p>';
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
                $gridRow[] = Mage::helper('loadtest')->__('Unknown');
                $gridRow[] = Mage::helper('loadtest')->__('Unknown');
                $gridRow[] = Mage::helper('loadtest')->__('Unknown');
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

        print '<p><a href="'.Mage::getUrl('*/index/').'">'.Mage::helper('loadtest')->__('Back to index').'</a></p>';
    }
}