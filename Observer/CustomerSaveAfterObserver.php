<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Observer;

use Halex\CustomerApproval\Model\ApprovalManager;
use Halex\CustomerApproval\Model\Config;
use Magento\Customer\Model\Data\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CustomerSaveAfterObserver
 */
class CustomerSaveAfterObserver implements ObserverInterface
{
    /**
     * @var Config
     */
    private Config $customerApprovalConfig;

    /**
     * @var ApprovalManager
     */
    private ApprovalManager $customerApprovalManager;

    /**
     * Constructor
     *
     * @param Config $customerApprovalConfig
     * @param ApprovalManager $customerApprovalManager
     */
    public function __construct(
        Config $customerApprovalConfig,
        ApprovalManager $customerApprovalManager
    ) {
        $this->customerApprovalConfig = $customerApprovalConfig;
        $this->customerApprovalManager = $customerApprovalManager;
    }

    /**
     * Execute method
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->customerApprovalConfig->isEnabled() || !$this->customerApprovalConfig->isNotifyEnabled()) {
            return;
        }

        /** @var Customer $customerSaved */
        $customerSaved = $observer->getCustomerDataObject();
        $customerSavedBefore = $observer->getOrigCustomerDataObject();

        if (!$customerSavedBefore || !$customerSaved) {
            return;
        }

        $previousCustomerApproveStatus = $customerSavedBefore
            ->getCustomAttributes()[Config::IS_APPROVED_ATTR_CODE]
            ->getValue();

        $customerApproveStatus = $customerSaved
            ->getCustomAttributes()[Config::IS_APPROVED_ATTR_CODE]
            ->getValue();

        if (
            $previousCustomerApproveStatus !== $customerApproveStatus
            && $customerApproveStatus === '1'
        ) {
            $this->customerApprovalManager->sendEmail(
                $customerSaved->getEmail(),
                $customerSaved->getFirstname(),
                $customerSaved->getStoreId()
            );
        }
    }
}
