<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Rule_Model_Action_Collection extends Magento_Rule_Model_Action_Abstract
{
    /**
     * @var Magento_Rule_Model_ActionFactory
     */
    protected $_actionFactory;

    /**
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Rule_Model_ActionFactory $actionFactory
     * @param Magento_Core_Model_Layout $layout
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Rule_Model_ActionFactory $actionFactory,
        Magento_Core_Model_Layout $layout,
        array $data = array()
    ) {
        $this->_actionFactory = $actionFactory;
        $this->_layout = $layout;

        parent::__construct($viewUrl, $data);

        $this->setActions(array());
        $this->setType('Magento_Rule_Model_Action_Collection');
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
     * @param array $arrAttributes
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
                $action = $this->_actionFactory->create($actArr['type']);
                $action->loadArray($actArr);
                $this->addAction($action);
            }
        }
        return $this;
    }

    public function addAction(Magento_Rule_Model_Action_Interface $action)
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
        return $this->getForm()->addField('action:' . $this->getId() . ':new_child', 'select', array(
            'name' => 'rule[actions][' . $this->getId() . '][new_child]',
            'values' => $this->getNewChildSelectOptions(),
            'value_name' => $this->getNewChildName(),
        ))->setRenderer($this->_layout->getBlockSingleton('Magento_Rule_Block_Newchild'));
    }

    /**
     * @return string
     */
    public function asHtmlRecursive()
    {
        $html = $this->asHtml() . '<ul id="action:' . $this->getId() . ':children">';
        foreach ($this->getActions() as $cond) {
            $html .= '<li>' . $cond->asHtmlRecursive() . '</li>';
        }
        $html .= '<li>' . $this->getNewChildElement()->getHtml() . '</li></ul>';
        return $html;
    }

    /**
     * @param string $format
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function asString($format = '')
    {
        $str = __("Perform following actions");
        return $str;
    }

    /**
     * @param int $level
     * @return string
     */
    public function asStringRecursive($level = 0)
    {
        $str = $this->asString();
        foreach ($this->getActions() as $action) {
            $str .= "\n" . $action->asStringRecursive($level + 1);
        }
        return $str;
    }

    /**
     * @return $this
     */
    public function process()
    {
        foreach ($this->getActions() as $action) {
            $action->process();
        }
        return $this;
    }
}
