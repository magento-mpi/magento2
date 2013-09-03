<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quote rule action abstract
 */
abstract class Magento_Rule_Model_Action_Abstract extends \Magento\Object implements Magento_Rule_Model_Action_Interface
{
    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param array $data
     */
    public function __construct(Magento_Core_Model_View_Url $viewUrl, array $data = array())
    {
        $this->_viewUrl = $viewUrl;

        parent::__construct($data);
        $this->loadAttributeOptions()->loadOperatorOptions()->loadValueOptions();

        foreach (array_keys($this->getAttributeOption()) as $attr) {
            $this->setAttribute($attr);
            break;
        }
        foreach (array_keys($this->getOperatorOption()) as $operator) {
            $this->setOperator($operator);
            break;
        }
    }

    public function getForm()
    {
        return $this->getRule()->getForm();
    }

    /**
     * @param array $arrAttributes
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function asArray(array $arrAttributes = array())
    {
        $out = array(
            'type' => $this->getType(),
            'attribute' => $this->getAttribute(),
            'operator' => $this->getOperator(),
            'value' => $this->getValue(),
        );
        return $out;
    }

    public function asXml()
    {
        $xml = "<type>" . $this->getType() . "</type>"
            . "<attribute>" . $this->getAttribute() . "</attribute>"
            . "<operator>" . $this->getOperator() . "</operator>"
            . "<value>" . $this->getValue() . "</value>";
        return $xml;
    }

    public function loadArray(array $arr)
    {
        $this->addData(array(
            'type' => $arr['type'],
            'attribute' => $arr['attribute'],
            'operator' => $arr['operator'],
            'value' => $arr['value'],
        ));
        $this->loadAttributeOptions();
        $this->loadOperatorOptions();
        $this->loadValueOptions();
        return $this;
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array());
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributeSelectOptions()
    {
        $opt = array();
        foreach ($this->getAttributeOption() as $key => $value) {
            $opt[] = array('value' => $key, 'label' => $value);
        }
        return $opt;
    }

    /**
     * @return string
     */
    public function getAttributeName()
    {
        return $this->getAttributeOption($this->getAttribute());
    }

    /**
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '=' => __('to'),
            '+=' => __('by'),
        ));
        return $this;
    }

    public function getOperatorSelectOptions()
    {
        $opt = array();
        foreach ($this->getOperatorOption() as $k=>$v) {
            $opt[] = array('value'=>$k, 'label'=>$v);
        }
        return $opt;
    }

    /**
     * @return string
     */
    public function getOperatorName()
    {
        return $this->getOperatorOption($this->getOperator());
    }

    /**
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array());
        return $this;
    }

    /**
     * @return array
     */
    public function getValueSelectOptions()
    {
        $opt = array();
        foreach ($this->getValueOption() as $key => $value) {
            $opt[] = array('value' => $key, 'label' => $value);
        }
        return $opt;
    }

    /**
     * @return string
     */
    public function getValueName()
    {
        $value = $this->getValue();
        return !empty($value) || 0 === $value ? $value : '...';
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(
            array(
                'value' => '',
                'label' => __('Please choose an action to add.')
            ),
        );
    }

    /**
     * @return string
     */
    public function getNewChildName()
    {
        return $this->getAddLinkHtml();
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        return '';
    }

    /**
     * @return string
     */
    public function asHtmlRecursive()
    {
        $str = $this->asHtml();
        return $str;
    }

    public function getTypeElement()
    {
        return $this->getForm()->addField('action:' . $this->getId() . ':type', 'hidden', array(
            'name' => 'rule[actions][' . $this->getId() . '][type]',
            'value' => $this->getType(),
            'no_span' => true,
        ));
    }

    public function getAttributeElement()
    {
        return $this->getForm()->addField('action:' . $this->getId() . ':attribute', 'select', array(
            'name' => 'rule[actions][' . $this->getId() . '][attribute]',
            'values' => $this->getAttributeSelectOptions(),
            'value' => $this->getAttribute(),
            'value_name' => $this->getAttributeName(),
        ))->setRenderer(Mage::getBlockSingleton('Magento_Rule_Block_Editable'));
    }

    public function getOperatorElement()
    {
        return $this->getForm()->addField('action:' . $this->getId() . ':operator', 'select', array(
            'name' => 'rule[actions][' . $this->getId() . '][operator]',
            'values' => $this->getOperatorSelectOptions(),
            'value' => $this->getOperator(),
            'value_name' => $this->getOperatorName(),
        ))->setRenderer(Mage::getBlockSingleton('Magento_Rule_Block_Editable'));
    }

    public function getValueElement()
    {
        return $this->getForm()->addField('action:' . $this->getId() . ':value', 'text', array(
            'name' => 'rule[actions][' . $this->getId() . '][value]',
            'value' => $this->getValue(),
            'value_name' => $this->getValueName(),
        ))->setRenderer(Mage::getBlockSingleton('Magento_Rule_Block_Editable'));
    }

    /**
     * @return string
     */
    public function getAddLinkHtml()
    {
        $src = $this->_viewUrl->getViewFileUrl('images/rule_component_add.gif');
        $html = '<img src="' . $src . '" alt="" class="rule-param-add v-middle" />';
        return $html;
    }

    /**
     * @return string
     */
    public function getRemoveLinkHtml()
    {
        $src = $this->_viewUrl->getViewFileUrl('images/rule_component_remove.gif');
        $html = '<span class="rule-param"><a href="javascript:void(0)" class="rule-param-remove"><img src="'
            . $src . '" alt="" class="v-middle" /></a></span>';
        return $html;
    }

    /**
     * @param string $format
     * @return string
     */
    public function asString($format = '')
    {
        return "";
    }

    /**
     * @param int $level
     * @return string
     */
    public function asStringRecursive($level = 0)
    {
        $str = str_pad('', $level * 3, ' ', STR_PAD_LEFT) . $this->asString();
        return $str;
    }

    /**
     * @return $this
     */
    public function process()
    {
        return $this;
    }
}
