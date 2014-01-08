<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart price rule chooser
 */
namespace Magento\CatalogRule\Block\Adminhtml\Promo\Widget;

class Chooser extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $_salesRule;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\SalesRule\Model\RuleFactory $salesRule
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\SalesRule\Model\RuleFactory $salesRule,
        array $data = array()
    ) {
        $this->_salesRule = $salesRule;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    /**
     * Block constructor, prepare grid params
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('rule_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('catalog_rule/promo_quote/chooser', array('uniq_id' => $uniqId));

        $chooser = $this->getLayout()->createBlock('Magento\Widget\Block\Adminhtml\Widget\Chooser')
            ->setElement($element)
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            $rule = $this->_salesRule->create()->load((int)$element->getValue());
            if ($rule->getId()) {
                $chooser->setLabel($rule->getName());
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var ruleName = trElement.down("td").next().innerHTML;
                var ruleId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                '.$chooserJsObject.'.setElementValue(ruleId);
                '.$chooserJsObject.'.setElementLabel(ruleName);
                '.$chooserJsObject.'.close();
            }
        ';
        return $js;
    }

    /**
     * Prepare rules collection
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->_salesRule->create()->getResourceCollection();
        $this->setCollection($collection);

        $this->_eventManager->dispatch('adminhtml_block_promo_widget_chooser_prepare_collection', array(
            'collection' => $collection
        ));

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for rules grid
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header'    => __('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header'    => __('Rule'),
            'align'     => 'left',
            'index'     => 'name',
        ));

        $this->addColumn('coupon_code', array(
            'header'    => __('Coupon Code'),
            'align'     => 'left',
            'width'     => '150px',
            'index'     => 'code',
        ));

        $this->addColumn('from_date', array(
            'header'    => __('Start on'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'    => __('End on'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('is_active', array(
            'header'    => __('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('catalog_rule/promo_quote/chooser', array('_current' => true));
    }
}
