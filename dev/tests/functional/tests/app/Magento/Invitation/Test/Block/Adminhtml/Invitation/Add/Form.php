<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Invitation\Test\Block\Adminhtml\Invitation\Add;

use Magento\Backend\Test\Block\Widget\Form as AbstractForm;
use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;

/**
 * New invitation form on backend.
 */
class Form extends AbstractForm
{
    /**
     * Fill invitations form.
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $data = $fixture->getData();
        $data['email'] = implode("\n", $data['email']);
        $mapping = $this->dataMapping($data);
        $this->_fill($mapping, $element);

        return $this;
    }
}
