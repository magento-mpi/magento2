<?php
namespace Magento\Ui\DataProvider;

use Magento\Framework\ObjectManager;

class Factory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param $providerClass
     * @return mixed
     */
    public function get($providerClass)
    {
        return $this->objectManager->get($providerClass);
    }
}
