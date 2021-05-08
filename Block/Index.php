<?php

namespace EvDev\UserStatus\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;

class Index extends Template
{

    /**
     * @var Session
     */
    public $customerSession;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepositoryInterface;

    /**
     * Info constructor.
     * @param Context $context
     * @param array $data
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerRepositoryInterface,
        Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerSession = $customerSession;
    }

    /**
     * @return string
     */
    public function getUserStatusAttributeValue(): string
    {
        try {
            $customerId = $this->customerSession->getCustomer()->getId();
            $customer = $this->customerRepositoryInterface->getById($customerId);
            $attribute = $customer->getCustomAttribute('customer_status');

            if (isset($attribute)) {
                return $attribute->getValue();
            }
        } catch (NoSuchEntityException | LocalizedException $e) {
        }
        return '';
    }
}
