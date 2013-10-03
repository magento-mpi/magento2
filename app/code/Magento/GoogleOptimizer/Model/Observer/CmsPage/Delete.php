<?php
/**
 * Google Experiment Cms Page Delete observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Model\Observer\CmsPage;

class Delete
{
    /**
     * @var \Magento\GoogleOptimizer\Model\Code
     */
    protected $_modelCode;

    /**
     * @param \Magento\GoogleOptimizer\Model\Code $modelCode
     */
    public function __construct(\Magento\GoogleOptimizer\Model\Code $modelCode)
    {
        $this->_modelCode = $modelCode;
    }

    /**
     * Delete Product scripts after deleting product
     *
     * @param \Magento\Object $observer
     * @return \Magento\GoogleOptimizer\Model\Observer\CmsPage\Delete
     */
    public function deleteCmsGoogleExperimentScript($observer)
    {
        /** @var $cmsPage \Magento\Cms\Model\Page */
        $cmsPage = $observer->getEvent()->getObject();
        $this->_modelCode->loadByEntityIdAndType(
            $cmsPage->getId(),
            \Magento\GoogleOptimizer\Model\Code::ENTITY_TYPE_PAGE
        );
        if ($this->_modelCode->getId()) {
            $this->_modelCode->delete();
        }
        return $this;
    }
}
