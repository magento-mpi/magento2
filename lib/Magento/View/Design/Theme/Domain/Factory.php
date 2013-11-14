<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme domain model class
 */
namespace Magento\View\Design\Theme\Domain;

use \Magento\View\Design\ThemeInterface;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_types = array(
        ThemeInterface::TYPE_PHYSICAL => 'Magento\Core\Model\Theme\Domain\Physical',
        ThemeInterface::TYPE_VIRTUAL  => 'Magento\Core\Model\Theme\Domain\Virtual',
        ThemeInterface::TYPE_STAGING  => 'Magento\Core\Model\Theme\Domain\Staging',
    );

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new config object
     *
     * @param \Magento\View\Design\ThemeInterface $theme
     * @return \Magento\Core\Model\Theme\Domain\Virtual|\Magento\Core\Model\Theme\Domain\Staging
     * @throws \Magento\Exception
     */
    public function create(ThemeInterface $theme)
    {
        if (!isset($this->_types[$theme->getType()])) {
            throw new \Magento\Exception(sprintf('Invalid type of theme domain model "%s"', $theme->getType()));
        }
        $class = $this->_types[$theme->getType()];
        return $this->_objectManager->create($class, array('theme' => $theme));
    }
}
