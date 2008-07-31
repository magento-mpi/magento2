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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile edit tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Extensions_Custom_Edit_Tab_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('extensions_custom_edit_grid');
//        $this->setDefaultSort('filename');
//        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        // take a look at Mage_Adminhtml_Model_Extension_Collection_Abstract
        $collection = Mage::getSingleton('backup/fs_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('filename', array(
            'header'    => Mage::helper('tag')->__('Package'),
            'index'     => 'filename',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/load', array(
            'id' => 'package_name, not filename here',
        ));
    }

    protected function _addColumnFilterToCollection($column)
    {
         if($column->getIndex()=='stores') {
                $this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
         } else {
                parent::_addColumnFilterToCollection($column);
         }

         return $this;
    }
}
