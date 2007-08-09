<?php

class Mage_Rule_Model_Condition_Combine extends Mage_Rule_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('rule/condition_combine')
            ->setAttribute('all')
            ->setValue(true)
            ->setConditions(array());
    }

    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'all' => __('ALL'),
            'any' => __('ANY'),
        ));
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            true => __('TRUE'),
            false => __('FALSE'),
        ));
        return $this;
    }

    public function addCondition(Mage_Rule_Model_Condition_Interface $condition)
    {
        $condition->setRule($this->getRule());
        $condition->setObject($this->getObject());

        $conditions = $this->getConditions();
        $conditions[] = $condition;

        if (!$condition->getId()) {
            $condition->setId($this->getId().'.'.sizeof($conditions));
        }

        $this->setConditions($conditions);
        return $this;
    }

    /**
     * Returns array containing conditions in the collection
     *
     * Output example:
     * array(
     *   'type'=>'combine',
     *   'operator'=>'ALL',
     *   'value'=>'TRUE',
     *   'conditions'=>array(
     *     {condition::asArray},
     *     {combine::asArray},
     *     {quote_item_combine::asArray}
     *   )
     * )
     *
     * @return array
     */
    public function asArray(array $arrAttributes = array())
    {
        $out = parent::asArray();

        foreach ($this->getConditions() as $condition) {
            $out['conditions'][] = $condition->asArray();
        }

        return $out;
    }

    public function asXml()
    {
        extract($this->asArray());
        $xml = "<attribute>".$this->getAttribute()."</attribute>"
            ."<operator>".$this->getOperator()."</operator>"
            ."<conditions>";
        foreach ($this->getConditions() as $condition) {
            $xml .= "<condition>".$condition->asXml()."</condition>";
        }
        $xml .= "</conditions>";
        return $xml;
    }

    public function loadArray($arr)
    {
        $this->setAttribute($arr['attribute'])
            ->setValue($arr['operator']);

        if (!empty($arr['conditions']) && is_array($arr['conditions'])) {
            foreach ($arr['conditions'] as $condArr) {
                $cond = Mage::getModel($condArr['type']);
                if (!empty($cond)) {
                    $this->addCondition($cond);
                    $cond->loadArray($condArr);
                }
            }
        }
        return $this;
    }

    public function loadXml($xml)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
        }
        $arr = parent::loadXml($xml);
        foreach ($xml->conditions->children() as $condition) {
            $arr['conditions'] = parent::loadXml($condition);
        }
        $this->loadArray($arr);
        return $this;
    }

    public function asHtml()
    {
       	$html = $this->getTypeElement()->getHtml().
       	    __("If %s of these conditions are %s:", $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml());
       	$html.= ' ('.$this->getNewChildElement()->getHtml().')';
       	if ($this->getId()!='1') {
       	    $html.= $this->getRemoveLinkHtml();
       	}
    	return $html;
    }

    public function getNewChildElement()
    {
    	return $this->getForm()->addField('cond:'.$this->getId().':new_child', 'select', array(
    		'name'=>'rule[conditions]['.$this->getId().'][new_child]',
    		'values'=>$this->getNewChildSelectOptions(),
    		'value_name'=>$this->getNewChildName(),
    	))->setRenderer(Mage::getHelper('rule/newchild'));
    }

    public function asHtmlRecursive()
    {
        $html = $this->asHtml().'<ul id="cond:'.$this->getId().':children" class="rule-param-children">';
        foreach ($this->getConditions() as $cond) {
            $html .= '<li>'.$cond->asHtmlRecursive().'</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function asString($format='')
    {
        $str = __("If %s of these conditions are %", $this->getAttributeName(), $this->getOperatorName());
        return $str;
    }

    public function asStringRecursive($level=0)
    {
        $str = parent::asStringRecursive($level);
        foreach ($this->getConditions() as $cond) {
            $str .= "\n".$cond->asStringRecursive($level+1);
        }
        return $str;
    }

    public function validate()
    {
        $all = $this->getAttribute()==='all';
        $true = (bool)$this->getOperator();
        foreach ($this->getConditions() as $cond) {
            if ($all && $cond->validate()!==$true) {
                return false;
            } elseif (!$all && $cond->validate()===$true) {
                return true;
            }
        }
        return $all ? true : false;
    }
}
