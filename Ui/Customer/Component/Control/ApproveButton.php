<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Ui\Customer\Component\Control;

use Halex\CustomerApproval\Model\ApprovalManager;
use Halex\CustomerApproval\Model\Config;
use Magento\Backend\Block\Widget\Context;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;

/**
 * Class ApproveButton
 */
class ApproveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var AccountManagementInterface
     */
    protected AccountManagementInterface $customerAccountManagement;

    /**
     * @var ApprovalManager
     */
    private ApprovalManager $customerApprovalManager;

    /**
     * @var AuthorizationInterface
     */
    private AuthorizationInterface $authorization;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param AccountManagementInterface $customerAccountManagement
     * @param ApprovalManager $customerApprovalManager
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AccountManagementInterface $customerAccountManagement,
        ApprovalManager $customerApprovalManager,
        AuthorizationInterface $authorization
    ) {
        parent::__construct(
            $context,
            $registry
        );

        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerApprovalManager = $customerApprovalManager;
        $this->authorization = $authorization;
    }

    /**
     * Get button data.
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getButtonData(): array
    {
        $customerId = $this->getCustomerId();

        if (
            $this->customerApprovalManager->isCustomerApproved($this->getCustomerId())
            || !$this->authorization->isAllowed(Config::ACL_APPROVE_ACTION)
        ) {
            return [];
        }

        $canModify = $customerId && !$this->customerAccountManagement->isReadonly($this->getCustomerId());
        $data = [];

        if ($customerId && $canModify) {
            $data = [
                'label' => __('Approve Customer'),
                'class' => 'approve',
                'id' => 'customer-approve-button',
                'data_attribute' => [
                    'url' => $this->getDeleteUrl(),
                ],
                'on_click' => '',
                'sort_order' => 20,
                'aclResource' => 'Halex_CustomerApproval::approve_customer',
            ];
        }

        return $data;
    }

    /**
     * Get delete url.
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('customer_approval/action/approve', ['entity_id' => $this->getCustomerId()]);
    }
}
