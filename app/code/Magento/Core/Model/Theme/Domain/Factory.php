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
namespace Magento\Core\Model\Theme\Domain;

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
        \Magento\Core\Model\Theme::TYPE_PHYSICAL => 'Magento\Core\Model\Theme\Domain\Physical',
        \Magento\Core\Model\Theme::TYPE_VIRTUAL  => 'Magento\Core\Model\Theme\Domain\Virtual',
        \Magento\Core\Model\Theme::TYPE_STAGING  => 'Magento\Core\Model\Theme\Domain\Staging',
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
     * @param \Magento\Core\Model\Theme $theme
     * @return \Magento\Core\Model\Theme\Domain\Virtual|\Magento\Core\Model\Theme\Domain\Staging
     * @throws \Magento\Core\Exception
     */
    public function create(\Magento\Core\Model\Theme $theme)
    {
        if (!isset($this->_types[$theme->getType()])) {
            throw new \Magento\Core\Exception(sprintf('Invalid type of theme domain model "%s"', $theme->getType()));
        }
        $class = $this->_types[$theme->getType()];
        return $this->_objectManager->create($class, array('theme' => $theme));
    }
}
