<?php
/**
 * Status column for Cache grid
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Block_Cache_Grid_Column_Statuses extends Mage_Backend_Block_Widget_Grid_Column
{
    /**
     * @var Mage_Core_Model_Cache_TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Core_Model_Cache_TypeListInterface $cacheTypeList
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Model_Cache_TypeListInterface $cacheTypeList,
        array $data = array()
    ) {
        parent::__construct ($context, $data);
        $this->_cacheTypeList = $cacheTypeList;
    }

    /**
     * Add to column decorated status
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return array($this, 'decorateStatus');
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param  Mage_Core_Model_Abstract $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $invalidedTypes = $this->_cacheTypeList->getInvalidated();
        if (isset($invalidedTypes[$row->getId()])) {
            $cell = '<span class="grid-severity-minor"><span>' . $this->__('Invalidated') . '</span></span>';
        } else {
            if ($row->getStatus()) {
                $cell = '<span class="grid-severity-notice"><span>' . $value . '</span></span>';
            } else {
                $cell = '<span class="grid-severity-critical"><span>' . $value . '</span></span>';
            }
        }
        return $cell;
    }
}
