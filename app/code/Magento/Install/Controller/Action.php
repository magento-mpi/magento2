<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Install\Controller;

class Action extends \Magento\Core\Controller\Varien\Action
{
    /**
     * @var \Magento\Core\Model\Config\Scope
     */
    protected $_configScope;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Config\Scope $configScope
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Config\Scope $configScope
    ) {
        $this->_configScope = $configScope;
        parent::__construct($context);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_configScope->setCurrentScope('install');
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
    }

    /**
     * Initialize area and design
     *
     * @return \Magento\Install\Controller\Action
     */
    protected function _initDesign()
    {
        $areaCode = $this->getLayout()->getArea();
        $area = \Mage::app()->getArea($areaCode);
        $area->load(\Magento\Core\Model\App\Area::PART_CONFIG);
        $this->_initDefaultTheme($areaCode);
        $area->detectDesign($this->getRequest());
        $area->load(\Magento\Core\Model\App\Area::PART_TRANSLATE);
        return $this;
    }

    /**
     * Initialize theme
     *
     * @param string $areaCode
     * @return \Magento\Install\Controller\Action
     */
    protected function _initDefaultTheme($areaCode)
    {
        /** @var $design \Magento\Core\Model\View\DesignInterface */
        $design = \Mage::getObjectManager()->get('Magento\Core\Model\View\DesignInterface');
        /** @var $themesCollection \Magento\Core\Model\Theme\Collection */
        $themesCollection = \Mage::getObjectManager()->create('Magento\Core\Model\Theme\Collection');
        $themeModel = $themesCollection->addDefaultPattern($areaCode)
            ->addFilter('theme_path', $design->getConfigurationDesignTheme($areaCode))
            ->getFirstItem();
        $design->setArea($areaCode)->setDesignTheme($themeModel);
        return $this;
    }
}
