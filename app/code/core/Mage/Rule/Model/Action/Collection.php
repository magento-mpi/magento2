<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Rule_Model_Action_Collection extends Mage_Rule_Model_Action_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setActions(array());
        $this->setType('Mage_Rule_Model_Action_Collection');
    }

    /**
     * Returns array containing actions in the collection
     *
     * Output example:
     * array(
     *   {action::asArray},
     *   {action::asArray}
     * )
     *
     * @return array
     */
    public function asArray(array $arrAttributes = array())
    {
        $out = parent::asArray();

        foreach ($this->getActions() as $item) {
            $out['actions'][] = $item->asArray();
        }
        return $out;
    }

    public function loadArray(array $arr)
    {
        if (!empty($arr['actions']) && is_array($arr['actions'])) {
            foreach ($arr['actions'] as $actArr) {
                if (empty($actArr['type'])) {
                    continue;
                }
                $action = Mage::getModel($actArr['type']);
                $action->loadArray($actArr);
                $this->addAction($action);
            }
        }
        return $this;
    }

    public function addAction(Mage_Rule_Model_Action_Interface $action)
    {
        $actions = $this->getActions();

        $action->setRule($this->getRule());

        $actions[] = $action;
        if (!$action->getId()) {
            $action->setId($this->getId().'.'.sizeof($actions));
        }

        $this->setActions($actions);
        return $this;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->toHtml().'Perform following actions: ';
        if ($this->getId()!='1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }
   public function getNewChildElement()
   {
       return $this->getForm()->addField('action:'.$this->getId().':new_child', 'select', array(
           'name'=>'rule[actions]['.$this->getId().'][new_child]',
           'values'=>$this->getNewChildSelectOptions(),
           'value_name'=>$this->getNewChildName(),
       ))->setRenderer(Mage::getBlockSingleton('Mage_Rule_Block_Newchild'));
    }

    public function asHtmlRecursive()
    {
        $html = $this->asHtml().'<ul id="action:'.$this->getId().':children">';
        foreach ($this->getActions() as $cond) {
            $html .= '<li>'.$cond->asHtmlRecursive().'</li>';
        }
        $html .= '<li>'.$this->getNewChildElement()->getHtml().'</li></ul>';
        return $html;
    }

    public function asString($format='')
    {
        $str = Mage::helper('Mage_Rule_Helper_Data')->__("Perform following actions");
        return $str;
    }

    public function asStringRecursive($level=0)
    {
        $str = $this->asString();
        foreach ($this->getActions() as $action) {
            $str .= "\n".$action->asStringRecursive($level+1);
        }
        return $str;
    }

    public function process()
    {
        foreach ($this->getActions() as $action) {
            $action->process();
        }
        return $this;
    }
}
