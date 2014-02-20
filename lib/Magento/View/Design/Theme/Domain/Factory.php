<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme\Domain;

use \Magento\View\Design\ThemeInterface;

/**
 * Theme domain model class factory
 */
class Factory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Types
     *
     * @var array
     */
    protected $_types = array(
        ThemeInterface::TYPE_PHYSICAL => 'Magento\View\Design\Theme\Domain\PhysicalInterface',
        ThemeInterface::TYPE_VIRTUAL  => 'Magento\View\Design\Theme\Domain\VirtualInterface',
        ThemeInterface::TYPE_STAGING  => 'Magento\View\Design\Theme\Domain\StagingInterface',
    );

    /**
     * Constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new config object
     *
     * @param ThemeInterface $theme
     * @return mixed
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
