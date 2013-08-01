<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */
class Saas_PrintedTemplate_Model_PageSize extends Magento_Object
{
    /**
     * Constructs page size by width height and name
     *
     * @param array $sizeInfo array(width => Zend_Measure_Length, height => Zend_Measure_Length, name => string)
     */
    public function __construct(array $sizeInfo)
    {
        $this->setWidth(isset($sizeInfo['width']) ? $sizeInfo['width'] : null)
            ->setHeight(isset($sizeInfo['height']) ? $sizeInfo['height'] : null)
            ->setName(isset($sizeInfo['name']) ? $sizeInfo['name'] : null);
    }

    /**
     * Set width of size
     *
     * @param Zend_Measure_Length $width
     * @return Saas_PrintedTemplate_Model_PageSize
     */
    public function setWidth(Zend_Measure_Length $width = null)
    {
        return $this->setData('width', $width);
    }

    /**
     * Set height of size
     *
     * @param Zend_Measure_Length $height
     * @return Saas_PrintedTemplate_Model_PageSize
     */
    public function setHeight(Zend_Measure_Length $height = null)
    {
        return $this->setData('height', $height);
    }
}
