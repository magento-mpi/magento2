<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\System\Config\Form\Fieldset\Order;

class Statuses extends \Magento\Backend\Block\System\Config\Form\Fieldset
{
    /**
     * Dummy element
     *
     * @var \Magento\Framework\Object
     */
    protected $_dummyElement;

    /**
     * Field renderer
     *
     * @var \Magento\Backend\Block\System\Config\Form\Field
     */
    protected $_fieldRenderer;

    /**
     * @var array
     */
    protected $_values;

    /**
     * Order status collection
     *
     * @var \Magento\Sales\Model\Resource\Order\Status\CollectionFactory
     */
    protected $_orderStatusCollection;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\View\Helper\Js $jsHelper
     * @param \Magento\Sales\Model\Resource\Order\Status\CollectionFactory $orderStatusCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        \Magento\Sales\Model\Resource\Order\Status\CollectionFactory $orderStatusCollection,
        array $data = array()
    ) {
        $this->_orderStatusCollection = $orderStatusCollection;
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    /**
     * Render elemnt
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '';

        $statuses = $this->_orderStatusCollection->create()->load()->toOptionHash();

        foreach ($statuses as $id => $status) {
            $html .= $this->_getFieldHtml($element, $id, $status);
        }
        return $html;
    }

    /**
     * Get dummy element
     *
     * @return \Magento\Framework\Object
     */
    protected function _getDummyElement()
    {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new \Magento\Framework\Object(array('showInDefault' => 1, 'showInWebsite' => 1));
        }
        return $this->_dummyElement;
    }

    /**
     * Get field renderer
     *
     * @return \Magento\Backend\Block\System\Config\Form\Field
     */
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = $this->getLayout()->getBlockSingleton(
                'Magento\Backend\Block\System\Config\Form\Field'
            );
        }
        return $this->_fieldRenderer;
    }

    /**
     * Get field html
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param string $id
     * @param string $status
     * @return string
     */
    protected function _getFieldHtml($fieldset, $id, $status)
    {
        $configData = $this->getConfigData();
        $path = 'sales/order_statuses/status_' . $id;
        //TODO: move as property of form
        $data = isset($configData[$path]) ? $configData[$path] : array();

        $e = $this->_getDummyElement();

        $field = $fieldset->addField(
            $id,
            'text',
            array(
                'name' => 'groups[order_statuses][fields][status_' . $id . '][value]',
                'label' => $status,
                'value' => isset($data['value']) ? $data['value'] : $status,
                'default_value' => isset($data['default_value']) ? $data['default_value'] : '',
                'old_value' => isset($data['old_value']) ? $data['old_value'] : '',
                'inherit' => isset($data['inherit']) ? $data['inherit'] : '',
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e)
            )
        )->setRenderer(
            $this->_getFieldRenderer()
        );

        return $field->toHtml();
    }
}
