<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Plugin;

use Halex\CustomerApproval\Model\Config;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\Data\CustomerInterface as CustomerData;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CustomerSession
 */
class CustomerSession
{
    /**
     * @var Config
     */
    private Config $customerApprovalConfig;

    /**
     * @var RedirectInterface
     */
    private RedirectInterface $redirect;

    /**
     * Constructor
     *
     * @param Config $customerApprovalConfig
     * @param RedirectInterface $redirect
     */
    public function __construct(
        Config $customerApprovalConfig,
        RedirectInterface $redirect,
    ) {
        $this->customerApprovalConfig = $customerApprovalConfig;
        $this->redirect = $redirect;
    }

    /**
     * @param Session $subject
     * @param callable $proceed
     * @param CustomerData $customer
     *
     * @return void
     * @throws LocalizedException
     */
    public function aroundSetCustomerDataAsLoggedIn(Session $subject, callable $proceed, $customer)
    {
        if (
            !$this->customerApprovalConfig->isEnabled()
            || str_contains($this->redirect->getRefererUrl(), 'customer/account/create')
        ) {
            return;
        }

        $isApprovedAttribute = $customer->getCustomAttribute(Config::IS_APPROVED_ATTR_CODE);

        if (!$isApprovedAttribute) {
            return $proceed($customer);
        }

        if (!(int)$isApprovedAttribute->getValue()) {
            throw new LocalizedException(
                __($this->customerApprovalConfig->getLoginErrorMessage() ?: 'This account is not approved.')
            );
        }

        return $proceed($customer);
    }
}
