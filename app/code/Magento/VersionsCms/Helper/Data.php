<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Helper;

/**
 * Base helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
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
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\User\Model\Resource\User\CollectionFactory $userCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
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
     * @param \Magento\Framework\Data\Form\AbstractForm $container
     * @param string $onChange
     * @param string|array $excludeTypes
     * @return void
     */
    public function addOnChangeToFormElements($container, $onChange, $excludeTypes = array('hidden'))
    {
        if (!is_array($excludeTypes)) {
            $excludeTypes = array($excludeTypes);
        }

        foreach ($container->getElements() as $element) {
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
