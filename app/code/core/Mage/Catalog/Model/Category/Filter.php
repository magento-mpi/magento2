<?php
/**
 * Category filter
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Category_Filter extends Varien_Data_Object 
{
    public function __construct($data=array()) 
    {
        parent::__construct($data);
    }
    
    public function getId()
    {
        return $this->getFilterId();
    }
    
    public function getResource()
    {
        static $resource;
        if (!$resource) {
            $resource = Mage::getModel('catalog_resource', 'category_filter');
        }
        return $resource;
    }
    
    public function load($filterId)
    {
        
    }

    public function useOption()
    {
        return $this->getUseOption();
    }
    
    public function getLabel()
    {
        return $this->getAttribute()->getCode();
    }
    
    public function getAttribute()
    {
        $data = array(
            'attribute_id'  => $this->getAttributeId(),
            'attribute_code'=> $this->getAttributeCode(),
            //'data_input'    => $this->getAttributeId(),
            'data_saver',
            'data_source',
            'data_type'     => $this->getDataType(),
            'validation',
            'input_format',
            'output_format',
            'required',
            'searchable',
            'comparale',
            'multiple'      => $this->getMultiple(),
            'delitable'
        );
        return Mage::getModel('catalog', 'product_attribute')->setData($data);
    }
    
    public function getValue()
    {
        $current    = $this->getCurrentValues();
        if (!empty($current)) {
            return  empty($current[$this->getId()]) ? array() : $current[$this->getId()];
        }
        else {
            return array();
        }
    }
    
    public function getLinks()
    {
        $arr = array();
        $available  = $this->getAvailableValues();
        $current    = $this->getCurrentValues();
        $currentValues = $this->getValue();
        
        $request = clone Mage::registry('controller')->getRequest();
        
        if ($this->useOption()) {
            $values = Mage::getModel('catalog', 'product_attribute')
                ->setAttributeId($this->getAttributeId())
                ->getOptions();
            foreach ($values as $value) {
                if (null !== $available) {
                    //var_dump($currentValues);
                    if (in_array($value->getId(), $available) && !in_array($value->getId(), $currentValues)) {
                        $arrParam = array('filter' => $current);
                        $arrParam['filter'][$this->getId()][] = $value->getId();
        
                        $arr[] = array(
                            'label' => $value->getValue(),
                            'url'   => Mage::getUrl('catalog', $request->setParam('array',$arrParam)->getParams()),
                        );
                    }
                }
                else {
                    $arr[] = array( 
                        'label' => $value->getValue(),
                        'url'   => Mage::getUrl(),
                    );
                }
            }
        }
        else {
            $values = $this->getResource()->getValues($this->getId());
            foreach ($values as $value) {
                
            }
        }
        return $arr;
    }
    
}