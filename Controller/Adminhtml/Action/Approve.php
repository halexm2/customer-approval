<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Controller\Adminhtml\Action;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Approve
 */
class Approve extends Action implements HttpGetActionInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * ACL
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Halex_CustomerApproval::approve_customer';

    /**
     * Constructor
     *
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);

        $this->customerRepository = $customerRepository;
    }

    /**
     * Run Cron Job
     *
     * @return Redirect
     * @throws LocalizedException
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerEntityId = $this->getRequest()->getParam('entity_id');

        if (!$customerEntityId) {
            $this->messageManager->addErrorMessage(
                __('Customer identifier must be specified!')
            );

            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }

        try {
            $customerEntity = $this->customerRepository->getById($customerEntityId);
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());

            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }

        $customerEntity->getExtensionAttributes()->setIsApproved(1);
        $this->customerRepository->save($customerEntity);

        $this->messageManager->addSuccessMessage(__('Customer has been approved.'));

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
