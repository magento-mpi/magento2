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
 * @package    Mage_Rule
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Abstract class for quote rule condition
 *
 */
abstract class Mage_Rule_Model_Condition_Abstract
	extends Varien_Object
	implements Mage_Rule_Model_Condition_Interface
{
    public function __construct()
    {
        parent::__construct();

        $this->loadAttributeOptions()->loadOperatorOptions()->loadValueOptions();

        if ($options = $this->getAttributeOptions()) {
            foreach ($options as $attr=>$dummy) { $this->setAttribute($attr); break; }
        }
        if ($options = $this->getOperatorOptions()) {
            foreach ($options as $operator=>$dummy) { $this->setOperator($operator); break; }
        }
    }

    public function getForm()
    {
        return $this->getRule()->getForm();
    }

    public function asArray(array $arrAttributes = array())
    {
        $out = array(
            'type'=>$this->getType(),
            'attribute'=>$this->getAttribute(),
            'operator'=>$this->getOperator(),
            'value'=>$this->getValue(),
        );
        return $out;
    }

    public function asXml()
    {
        extract($this->toArray());
        $xml = "<type>".$this->getType()."</type>"
            ."<attribute>".$this->getAttribute()."</attribute>"
            ."<operator>".$this->getOperator()."</operator>"
            ."<value>".$this->getValue()."</value>";
        return $xml;
    }

    public function loadArray($arr)
    {
        $this->addData(array(
            'type'=>$arr['type'],
            'attribute'=>isset($arr['attribute']) ? $arr['attribute'] : false,
            'operator'=>isset($arr['operator']) ? $arr['operator'] : false,
            'value'=>isset($arr['value']) ? $arr['value'] : false,
        ));
        $this->loadAttributeOptions();
        $this->loadOperatorOptions();
        $this->loadValueOptions();
        return $this;
    }

    public function loadXml($xml)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
        }
        $arr = (array)$xml;
        $this->loadArray($arr);
        return $this;
    }

    public function getAttributeOptions()
    {
        return array();
    }

    public function getAttributeSelectOptions()
    {
    	$opt = array();
    	foreach ($this->getAttributeOption() as $k=>$v) {
    		$opt[] = array('value'=>$k, 'label'=>$v);
    	}
    	return $opt;
    }

    public function getAttributeName()
    {
        return $this->getAttributeOption($this->getAttribute());
    }

    public function loadOperatorOptions()
    {
        $hlp = Mage::helper('rule');
        $this->setOperatorOption(array(
            '=='  => $hlp->__('is'),
            '!='  => $hlp->__('is not'),
            '>='  => $hlp->__('equals or greater than'),
            '<='  => $hlp->__('equals or less than'),
            '>'   => $hlp->__('greater than'),
            '<'   => $hlp->__('less than'),
            '{}'  => $hlp->__('contains'),
            '!{}' => $hlp->__('does not contain'),
            '()'  => $hlp->__('is one of'),
            '!()' => $hlp->__('is not one of'),
        ));
        $this->setOperatorByInputType(array(
            'string' => array('==', '!=', '>=', '>', '<=', '<', '{}', '!{}', '()', '!()'),
            'numeric' => array('==', '!=', '>=', '>', '<=', '<', '()', '!()'),
            'date' => array('==', '>=', '<='),
            'select' => array('==', '!='),
            'grid' => array('()', '!()'),
        ));
        return $this;
    }

    /**
     * This value will define which operators will be available for this condition.
     *
     * Possible values are: string, numeric, date, select, multiselect, grid
     *
     * @return string
     */
    public function getInputType()
    {
        return 'string';
    }

    public function getOperatorSelectOptions()
    {
        $type = $this->getInputType();
    	$opt = array();
    	$operatorByType = $this->getOperatorByInputType();
    	foreach ($this->getOperatorOption() as $k=>$v) {
    	    if (!$operatorByType || in_array($k, $operatorByType[$type])) {
    		    $opt[] = array('value'=>$k, 'label'=>$v);
    	    }
    	}
    	return $opt;
    }

    public function getOperatorName()
    {
        return $this->getOperatorOption($this->getOperator());
    }

    public function loadValueOptions()
    {
//        $this->setValueOption(array(
//            true  => Mage::helper('rule')->__('TRUE'),
//            false => Mage::helper('rule')->__('FALSE'),
//        ));
        $this->setValueOption(array());
        return $this;
    }

    public function getValueSelectOptions()
    {
    	$opt = array();
    	foreach ($this->getValueOption() as $k=>$v) {
    		$opt[] = array('value'=>$k, 'label'=>$v);
    	}
    	return $opt;
    }

    public function getValueName()
    {
        $value = $this->getValue();
        if (is_null($value) || ''===$value) {
            return '...';
        }
        $options = $this->getValueOption();
        if (!empty($options) && !empty($options[$value])) {
            $value = $this->getValueOption($value);
        }
        return $value;
    }

    public function getNewChildSelectOptions()
    {
        return array(
            array('value'=>'', 'label'=>Mage::helper('rule')->__('Please choose a condition to add...')),
        );
    }

    public function getNewChildName()
    {
        return $this->getAddLinkHtml();
    }

    public function asHtml()
    {
    	$html = $this->getTypeElementHtml()
    	   .$this->getAttributeElementHtml()
           .$this->getOperatorElementHtml()
    	   .$this->getValueElementHtml()
    	   .$this->getRemoveLinkHtml()
    	   .$this->getChooserContainerHtml();
    	return $html;
    }

    public function asHtmlRecursive()
    {
        $html = $this->asHtml();
        return $html;
    }

    public function getTypeElement()
    {
    	return $this->getForm()->addField('cond:'.$this->getId().':type', 'hidden', array(
    		'name'=>'rule[conditions]['.$this->getId().'][type]',
    		'value'=>$this->getType(),
    		'no_span'=>true,
    	));
    }

    public function getTypeElementHtml()
    {
        return $this->getTypeElement()->getHtml();
    }

    public function getAttributeElement()
    {
        if (is_null($this->getAttribute())) {
            foreach ($this->getAttributeOption() as $k=>$v) {
                $this->setAttribute($k);
                break;
            }
        }
    	return $this->getForm()->addField('cond:'.$this->getId().':attribute', 'select', array(
    		'name'=>'rule[conditions]['.$this->getId().'][attribute]',
    		'values'=>$this->getAttributeSelectOptions(),
    		'value'=>$this->getAttribute(),
    		'value_name'=>$this->getAttributeName(),
    	))->setRenderer(Mage::getHelper('rule/editable'));
    }

    public function getAttributeElementHtml()
    {
        return $this->getAttributeElement()->getHtml();
    }

    public function getOperatorElement()
    {
        if (is_null($this->getOperator())) {
            foreach ($this->getOperatorOption() as $k=>$v) {
                $this->setOperator($k);
                break;
            }
        }
        return $this->getForm()->addField('cond:'.$this->getId().':operator', 'select', array(
    		'name'=>'rule[conditions]['.$this->getId().'][operator]',
    		'values'=>$this->getOperatorSelectOptions(),
    		'value'=>$this->getOperator(),
    		'value_name'=>$this->getOperatorName(),
    	))->setRenderer(Mage::getHelper('rule/editable'));
    }

    public function getOperatorElementHtml()
    {
        return $this->getOperatorElement()->getHtml();
    }

    /**
     * Value element type will define renderer for condition value element
     *
     * @see Varien_Data_Form_Element
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    public function getValueElementRenderer()
    {
        if (strpos($this->getValueElementType(), '/')!==false) {
            return Mage::getHelper($this->getValueElementType());
        }
        return Mage::getHelper('rule/editable');
    }

    public function getValueElement()
    {
        return $this->getForm()->addField('cond:'.$this->getId().':value', $this->getValueElementType(), array(
    		'name'=>'rule[conditions]['.$this->getId().'][value]',
    		'value'=>$this->getValue(),
    		'values'=>$this->getValueSelectOptions(),
    		'value_name'=>$this->getValueName(),
    		'explicit_apply'=>$this->getExplicitApply(),
    	))->setRenderer($this->getValueElementRenderer());
    }

    public function getValueElementHtml()
    {
        return $this->getValueElement()->getHtml();
    }

    public function getAddLinkHtml()
    {
    	$src = Mage::getDesign()->getSkinUrl('images/rule_component_add.gif');
    	$html = '<img src="'.$src.'" class="rule-param-add v-middle"/>';
        return $html;
    }

    public function getRemoveLinkHtml()
    {
    	$src = Mage::getDesign()->getSkinUrl('images/rule_component_remove.gif');
        $html = ' <span class="rule-param"><a href="javascript:void(0)" class="rule-param-remove"><img src="'.$src.'" class="v-middle"/></a></span>';
        return $html;
    }

    public function getChooserContainerHtml()
    {
        $url = $this->getValueElementChooserUrl();
        $html = '';
        if ($url) {
            $html = '<div class="rule-chooser" url="'.$url.'"></div>';
        }
        return $html;
    }

    public function asString($format='')
    {
        $str = $this->getAttributeName().' '.$this->getOperatorName().' '.$this->getValueName();
        return $str;
    }

    public function asStringRecursive($level=0)
    {
        $str = str_pad('', $level*3, ' ', STR_PAD_LEFT).$this->asString();
        return $str;
    }

    public function validateAttribute($validatedValue)
    {
        // $validatedValue suppose to be simple alphanumeric value
        if (is_array($validatedValue) || is_object($validatedValue)) {
            return false;
        }

        $op = $this->getOperator();

        // if operator requires array and it is not, or on opposite, return false
        if ((($op=='()' || $op=='!()') && !is_array($this->getValue()))
            || (!($op=='()' || $op=='!()') && is_array($this->getValue()))) {
            return false;
        }

        $result = false;

        switch ($op) {
            case '==': case '!=':
                $result = $validatedValue==$this->getValue();
                break;

            case '<=': case '>':
                $result = $validatedValue<=$this->getValue();
                break;

            case '>=': case '<':
                $result = $validatedValue>=$this->getValue();
                break;

            case '{}': case '!{}':
                $result = stripos((string)$validatedValue, (string)$this->getValue())!==false;
                break;

            case '()': case '!()':
                $result = in_array($validatedValue, (array)$this->getValue());
                break;
        }

        if ('!='==$op || '>'==$op || '<'==$op || '!{}'==$op || '!()'==$op) {
            $result = !$result;
        }

        return $result;
    }

    public function validate(Varien_Object $object)
    {
        return $this->validateAttribute($object->getData($this->getAttribute()));
    }
}