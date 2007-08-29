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
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Currency grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Currency_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() 
    {
        parent::__construct();
        $this->setId('currency_grid');
        $this->setDefaultFilter(array('language'=>Mage::getStoreConfig('general/local/language')));
        //$this->setSaveParametersInSession(false);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('directory/currency_collection')
            ->joinRates(Mage::getStoreConfig('general/currency/base'));
            //->addLanguageFilter('en');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $baseUrl = Mage::getUrl();

        $this->addColumn('code', array(
            'header'=>__('Code'),
            'width' =>'70px',
            'index' =>'currency_code',
        ));
        
        $this->addColumn('rate', array(
            'header'=>__('Rate With %s', Mage::getStoreConfig('general/currency/base')),
            'type'  => 'number',
            'index' => 'rate',
        ));

        $language = Mage::getResourceModel('core/language_collection')->load()->toOptionHash();

        $this->addColumn('language', array(
            'header'=>__('Language'),
            'width' => '120px',
            'index' =>'language_code',
            'type' => 'options',
            'options' => $language,
            
        ));

        $this->addColumn('name', array(
            'header'=>__('Name'),
            'index' =>'currency_name'
        ));
        
        $this->addColumn('format', array(
            'header'=>__('Format'),
            'index'=>'output_format',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return false;
        //return Mage::getUrl('*/*/edit', array('currency' => $row->getId()));
    }
}
