<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base helper
 */
namespace Magento\VersionsCms\Helper;

class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Array of admin users in system
     *
     * @var array
     */
    protected $_usersHash = null;

    /**
     * @var \Magento\User\Model\Resource\User\CollectionFactory
     */
    protected $_userCollectionFactory;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\User\Model\Resource\User\CollectionFactory $userCollectionFactory
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\User\Model\Resource\User\CollectionFactory $userCollectionFactory
    ) {
        $this->_userCollectionFactory = $userCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Retrieve array of admin users in system
     *
     * @param bool $addEmptyUser
     * @return array
     */
    public function getUsersArray($addEmptyUser = false)
    {
        if (!$this->_usersHash) {
            $this->_usersHash = array();

            if ($addEmptyUser) {
                $this->_usersHash[''] = '';
            }

            foreach ($this->_userCollectionFactory->create() as $user) {
                $this->_usersHash[$user->getId()] = $user->getUsername();
            }
        }

        return $this->_usersHash;
    }

    /**
     * Get version's access levels with labels.
     *
     * @return array
     */
    public function getVersionAccessLevels()
    {
        return array(
            \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PRIVATE => __('Private'),
            \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PROTECTED => __('Protected'),
            \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC => __('Public')
        );
    }

    /**
     * Recursively walk through container (form or fieldset)
     * and add to each element new onChange method.
     * Element will be skipped if its type passed in $excludeTypes parameter.
     *
     * @param \Magento\Data\Form\AbstractForm $container
     * @param string $onChange
     * @param string|array $excludeTypes
     */
    public function addOnChangeToFormElements($container, $onChange, $excludeTypes = array('hidden'))
    {
        if (!is_array($excludeTypes)) {
            $excludeTypes = array($excludeTypes);
        }

        foreach ($container->getElements()as $element) {
            if ($element->getType() == 'fieldset') {
                $this->addOnChangeToFormElements($element, $onChange, $excludeTypes);
            } else {
                if (!in_array($element->getType(), $excludeTypes)) {
                    if ($element->hasOnchange()) {
                        $onChangeBefore = $element->getOnchange() . ';';
                    } else {
                        $onChangeBefore = '';
                    }
                    $element->setOnchange($onChangeBefore . $onChange);
                }
            }
        }
    }

    /**
     * Get 'Delete Multiple Hierarchies' text
     *
     * @return string
     */
    public function getDeleteMultipleHierarchiesText()
    {
        return __('Delete Multiple Hierarchies');
    }
}
