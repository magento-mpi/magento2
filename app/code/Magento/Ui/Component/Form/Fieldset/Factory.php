<?php
namespace Magento\Ui\Component\Form\Fieldset;

use Magento\Framework\ObjectManager;
use Magento\Ui\Component\Form\Fieldset;

/**
 * Class Factory
 */
class Factory
{
    /**
     * @var string
     */
    protected $className = 'Magento\Ui\Component\Form\Fieldset';

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
     * Create data provider
     *
     * @param array $arguments
     * @return Fieldset
     */
    public function create(array $arguments = [])
    {
        return $this->objectManager->create($this->className, ['data' => $arguments]);
    }
}
