<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Block;

/**
 * @method string|array getInputNames()
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Serializer extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $grid = $this->getGridBlock();
        if (is_string($grid)) {
            $grid = $this->getLayout()->getBlock($grid);
        }
        if ($grid instanceof \Magento\Ui\Listing\View) {
            $this->setGridBlock($grid)->setSerializeData($grid->{$this->getCallback()}());
        }
        return parent::_prepareLayout();
    }

    /**
     * Set serializer template
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Magento_Ui::grid/serializer.phtml');
    }

    /**
     * Get grid column input names to serialize
     *
     * @param bool $asJSON
     * @return string|array
     */
    public function getColumnInputNames($asJSON = false)
    {
        if ($asJSON) {
            return $this->_jsonEncoder->encode((array)$this->getInputNames());
        }
        return (array)$this->getInputNames();
    }

    /**
     * Get object data as JSON
     *
     * @return string
     */
    public function getDataAsJSON()
    {
        $result = array();
        $inputNames = $this->getInputNames();
        if ($serializeData = $this->getSerializeData()) {
            $result = $serializeData;
        } elseif (!empty($inputNames)) {
            return '{}';
        }
        return $this->_jsonEncoder->encode($result);
    }
}
