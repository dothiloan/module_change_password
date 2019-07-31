<?php
namespace LOANDT\Customer\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Customer\Api\CustomerMetadataInterface;

class Data extends AbstractHelper 
{
    protected $request;
    public function __construct(
       \Magento\Framework\App\Action\Action $request
    ) {
       $this->request = $request;

    }

    /**
     * Get email notification
     *
     * @return EmailNotificationInterface
     * @deprecated 100.1.0
     */
    public function getEmailNotification()
    {
        if (!($this->emailNotification instanceof EmailNotificationInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                EmailNotificationInterface::class
            );
        } else {
            return $this->emailNotification;
        }
    }

    /**
     * Get metadata form
     *
     * @param string $entityType
     * @param string $formCode
     * @param string $scope
     * @return Form
     */
    public function getMetadataForm($entityType, $formCode, $scope)
    {
        $attributeValues = [];

        if ($entityType == CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER) {
            $customerId = $this->getCurrentCustomerId();
            if ($customerId) {
                $customer = $this->_customerRepository->getById($customerId);
                $attributeValues = $this->customerMapper->toFlatArray($customer);
            }
        }

        if ($entityType == AddressMetadataInterface::ENTITY_TYPE_ADDRESS) {
            $scopeData = explode('/', $scope);
            if (isset($scopeData[1]) && is_numeric($scopeData[1])) {
                $customerAddress = $this->addressRepository->getById($scopeData[1]);
                $attributeValues = $this->addressMapper->toFlatArray($customerAddress);
            }
        }

        $metadataForm = $this->_formFactory->create(
            $entityType,
            $formCode,
            $attributeValues,
            false,
            Form::DONT_IGNORE_INVISIBLE
        );

        return $metadataForm;
    }

    /**
     * Retrieve current customer ID
     *
     * @return int
     */
    public function getCurrentCustomerId()
    {
        $originalRequestData = $this->request->getRequest()->getPostValue(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER);

        $customerId = isset($originalRequestData['entity_id'])
            ? $originalRequestData['entity_id']
            : null;

        return $customerId;
    }

    /**
     * Disable Customer Address Validation
     *
     * @param CustomerInterface $customer
     * @throws NoSuchEntityException
     */
    public function disableAddressValidation($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            $addressModel = $this->addressRegistry->retrieve($address->getId());
            $addressModel->setShouldIgnoreValidation(true);
        }
    }
}