<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Mail\Template;

class TransportBuilderMock extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @var \Magento\Framework\Mail\Message
     */
    protected $_sentMessage;

    /**
     * Reset object state
     *
     * @return $this
     */
    protected function reset()
    {
        $this->_sentMessage = $this->message;
        parent::reset();
    }

    /**
     * Returns message object with prepared data
     *
     * @return \Magento\Framework\Mail\Message|null
     */
    public function getSentMessage()
    {
        return $this->_sentMessage;
    }
}
