<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Ui\Component\Form\Element;

/**
 * Class Multiline
 */
class Multiline extends AbstractFormElement
{
    /**
     * @return mixed|string
     */
    public function getType()
    {
        return $this->getData('input_type') ? $this->getData('input_type') : 'text';
    }

    /**
     * @return void
     */
    public function prepare()
    {
        parent::prepare(); // TODO: Change the autogenerated stub
    }
}
