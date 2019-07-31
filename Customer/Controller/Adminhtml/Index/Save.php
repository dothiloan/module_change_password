<?php
/**
 * Copyright Â© Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace LOANDT\Customer\Controller\Adminhtml\Index;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Model\AddressRegistry;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Customer\Model\Metadata\Form;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ObjectManager;
use LOANDT\Customer\Helper\Data;

/**
 * Save customer action.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Customer\Controller\Adminhtml\Index\Save
{
    /**
     * @var EmailNotificationInterface
     */
    private $emailNotification;

    /**
     * @var AddressRegistry
     */
    private $addressRegistry;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param \Magento\Framework\Math\Random $random
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param Mapper $addressMapper
     * @param AccountManagementInterface $customerAccountManagement
     * @param AddressRepositoryInterface $addressRepository
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param AddressInterfaceFactory $addressDataFactory
     * @param \Magento\Customer\Model\Customer\Mapper $customerMapper
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param ObjectFactory $objectFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param AddressRegistry|null $addressRegistry
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Helper\View $viewHelper,
        \Magento\Framework\Math\Random $random,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        Mapper $addressMapper,
        AccountManagementInterface $customerAccountManagement,
        AddressRepositoryInterface $addressRepository,
        CustomerInterfaceFactory $customerDataFactory,
        AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        ObjectFactory $objectFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        AddressRegistry $addressRegistry = null,
        Data $helper
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $customerFactory,
            $addressFactory,
            $formFactory,
            $subscriberFactory,
            $viewHelper,
            $random,
            $customerRepository,
            $extensibleDataObjectConverter,
            $addressMapper,
            $customerAccountManagement,
            $addressRepository,
            $customerDataFactory,
            $addressDataFactory,
            $customerMapper,
            $dataObjectProcessor,
            $dataObjectHelper,
            $objectFactory,
            $layoutFactory,
            $resultLayoutFactory,
            $resultPageFactory,
            $resultForwardFactory,
            $resultJsonFactory
        );
        $this->addressRegistry = $addressRegistry ?: ObjectManager::getInstance()->get(AddressRegistry::class);
        $this->helper = $helper;
    }

    /**
     * Save customer action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $returnToEdit = false;
        $originalRequestData = $this->getRequest()->getPostValue();
       
        $customerId = $this->helper->getCurrentCustomerId();

        if ($originalRequestData) {
            try {
                // optional fields might be set in request for future processing by observers in other modules
                $customerData = $this->_extractCustomerData();

                $data = $this->getRequest()->getParams();
				$passwordReset = $data['customer']['password_hash'];
				$objectHash = $this->_objectManager->create('Magento\Framework\Encryption\EncryptorInterface');
                $hash_password = $objectHash->getHash($passwordReset, true);

				// $customerData['password_hash'] = $has_password;

                if ($customerId) {
                    $currentCustomer = $this->_customerRepository->getById($customerId);
                    // No need to validate customer address while editing customer profile
                    $this->helper->disableAddressValidation($currentCustomer);
                    $customerData = array_merge(
                        $this->customerMapper->toFlatArray($currentCustomer),
                        $customerData
                    );
                    $customerData['id'] = $customerId;
                }

                /** @var CustomerInterface $customer */
                $customer = $this->customerDataFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $customer,
                    $customerData,
                    \Magento\Customer\Api\Data\CustomerInterface::class
                );

                $this->_eventManager->dispatch(
                    'adminhtml_customer_prepare_save',
                    ['customer' => $customer, 'request' => $this->getRequest()]
                );

                if (isset($customerData['sendemail_store_id'])) {
                    $customer->setStoreId($customerData['sendemail_store_id']);
                }

                // Save customer
                if ($customerId) {

                    $this->_customerRepository->save($customer, $hash_password);

                    $this->helper->getEmailNotification()->credentialsChanged($customer, $currentCustomer->getEmail());
                } else {
                    $customer = $this->customerAccountManagement->createAccountWithPasswordHash($customer, $hash_password);
                    $customerId = $customer->getId();
                }

                $isSubscribed = null;
                if ($this->_authorization->isAllowed(null)) {
                    $isSubscribed = $this->getRequest()->getPost('subscription');
                }
                if ($isSubscribed !== null) {
                    if ($isSubscribed !== '0') {
                        $this->_subscriberFactory->create()->subscribeCustomerById($customerId);
                    } else {
                        $this->_subscriberFactory->create()->unsubscribeCustomerById($customerId);
                    }
                }

                // After save
                $this->_eventManager->dispatch(
                    'adminhtml_customer_save_after',
                    ['customer' => $customer, 'request' => $this->getRequest()]
                );
                $this->_getSession()->unsCustomerFormData();
                // Done Saving customer, finish save action
                $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
                $this->messageManager->addSuccess(__('You saved the customer.'));
                $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
            } catch (\Magento\Framework\Validator\Exception $exception) {
                $messages = $exception->getMessages();
                if (empty($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $this->_getSession()->setCustomerFormData($originalRequestData);
                $returnToEdit = true;
            } catch (\Magento\Framework\Exception\AbstractAggregateException $exception) {
                $errors = $exception->getErrors();
                $messages = [];
                foreach ($errors as $error) {
                    $messages[] = $error->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $this->_getSession()->setCustomerFormData($originalRequestData);
                $returnToEdit = true;
            } catch (LocalizedException $exception) {
                $this->_addSessionErrorMessages($exception->getMessage());
                $this->_getSession()->setCustomerFormData($originalRequestData);
                $returnToEdit = true;
            } catch (\Exception $exception) {
                //$this->messageManager->addException($exception, __('Something went wrong while saving the customer.'));
                $this->_getSession()->setCustomerFormData($originalRequestData);
                $returnToEdit = true;
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($returnToEdit) {
            if ($customerId) {
                $resultRedirect->setPath(
                    'customer/*/edit',
                    ['id' => $customerId, '_current' => true]
                );
            } else {
                $resultRedirect->setPath(
                    'customer/*/new',
                    ['_current' => true]
                );
            }
        } else {
            $resultRedirect->setPath('customer/index');
        }
        return $resultRedirect;
    }

    
}
