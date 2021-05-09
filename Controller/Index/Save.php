<?php
namespace EvDev\UserStatus\Controller\Index;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class Save
 * @package EvDev\UserStatus\Controller\Index
 */
class Save extends Index implements HttpPostActionInterface
{
    /** @var PageFactory */
    protected $resultPageFactory;
    protected $resultRedirect;
    protected $customer;
    public $customerSession;
    protected $customerRepositoryInterface;
    protected $request;

    /**
     * Save constructor.
     * @param PageFactory $resultPageFactory
     * @param CustomerInterface $customerInterface
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Request $request
     * @param RedirectFactory $resultRedirect
     */
    public function __construct(
        PageFactory $resultPageFactory,
        CustomerInterface $customerInterface,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepositoryInterface,
        Request $request,
        RedirectFactory $resultRedirect
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customer = $customerInterface;
        $this->customerSession = $customerSession;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->request = $request;
        $this->resultRedirect = $resultRedirect;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {
        $this->customer = $this->getCustomer();
        if (empty($this->customer)) {
            return $this->resultRedirect->create()
                ->setPath('customer/account/login');
        }

        $data = $this->getPostData();
        if (sizeof($data) > 0) {
            $this->saveAttribute($data);
        }

        return $this->resultRedirect->create()
            ->setPath('userstatus');
    }

    /**
     * @param $data
     */
    protected function saveAttribute($data)
    {
        if (is_array($data) && !empty($data)) {

            foreach ($data as $key => $val) {
                $this->customer->setCustomAttribute($key, $val);
            }

            try {
                $this->customerRepositoryInterface->save($this->customer);
            } catch (InputException | InputMismatchException | LocalizedException $e) {
            }
        }

    }

    /**
     * @return mixed
     */
    protected function getPostData()
    {
        return $this->request->getPostValue();
    }

    /**
     * @return CustomerInterface|string
     */
    protected function getCustomer()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        if (isset($customerId)) {
            try {
                return $this->customerRepositoryInterface->getById($customerId);
            } catch (NoSuchEntityException | LocalizedException $e) {
            }
        }
        return '';
    }
}
