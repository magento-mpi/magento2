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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product options text type block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_View_Options_Type_Select
    extends Mage_Catalog_Block_Product_View_Options_Abstract
{
    /**
     * Enter description here...
     *
     * @return Mage_Catalog_Model_Product_Option_Value
     */
    public function getValuesCollection()
    {
        $collection = Mage::getSingleton('catalog/product_option_value')
            ->getValuesCollection($this->getOption()->getOptionId(), $this->getStoreId())//!!!!!!!!!!!
            ->setOrder('option_type_id', 'asc')
            ->load(false);

        return $collection;
    }

    public function getValuesHtml()
    {
        $collection = $this->getValuesCollection();

        if ($this->getOption()->getType() == 'drop_down' || $this->getOption()->getType() == 'multiple') {
            $require = ($this->getOption()->getIsRequire()) ? ' required-entry' : '';
            $select = $this->getLayout()->createBlock('core/html_select')
                ->setData(array(
                    'id' => 'drop_down',
                    'class' => 'select'.$require
                ))
                ->setName('options['.$this->getOption()->getid().']');
            $select->addOption('', $this->__('-- Please Select --'));
            foreach ($collection as $_value) {
                $select->addOption($_value->getOptionTypeId(), $_value->getTitle());
            }

            if ($this->getOption()->getType() == 'multiple') {
                $select->setExtraParams('multiple="multiple"');
            }

            return $select->getHtml();
        }

        if ($this->getOption()->getType() == 'radio' || $this->getOption()->getType() == 'checkbox') {
            $require = ($this->getOption()->getIsRequire()) ? ' validate-one-required' : '';
            switch ($this->getOption()->getType()) {
                case 'radio':
                    $type = 'radio';
                    break;
                case 'checkbox':
                    $type = 'checkbox';
                    break;
            }
            $selectHtml = '';
            foreach ($collection as $_value) {
                $selectHtml .= '<input type="'.$type.'" class="'.$require.'" id="" name="options['.$this->getOption()->getId().']" value="'.$_value->getOptionTypeId().'">'.$_value->getTitle().'<br />';
            }

            return $selectHtml;
        }
    }

}