<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Design\Backend;

class Theme extends \Magento\Core\Model\Config\Value
{
    /**
     * Design package instance
     *
     * @var \Magento\Core\Model\View\DesignInterface
     */
    protected $_design = null;

    /**
     * @param \Magento\Core\Model\View\DesignInterface $design
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\View\DesignInterface $design,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_design = $design;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Validate specified value against frontend area
     */
    protected function _beforeSave()
    {
        $design = clone $this->_design;
        $design->setDesignTheme($this->getValue(), \Magento\Core\Model\App\Area::AREA_FRONTEND);
        return parent::_beforeSave();
    }
}
