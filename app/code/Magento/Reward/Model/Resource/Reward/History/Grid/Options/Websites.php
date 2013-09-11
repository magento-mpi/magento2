<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Resource\Reward\History\Grid\Options;

class Websites
    implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * System Store Model
     *
     * @var \Magento\Reward\Model\Source\Website
     */
    protected $_systemStore;

    /**
     * @param \Magento\Reward\Model\Source\Website
     */
    public function __construct(\Magento\Reward\Model\Source\Website $systemStore)
    {
        $this->_systemStore = $systemStore;
    }

    /**
     * Return websites array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->toOptionArray(false);
    }
}
