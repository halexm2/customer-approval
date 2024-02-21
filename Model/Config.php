<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var string
     */
    public const XML_PATH_CUSTOMER_APPROVAL_ENABLED = 'customer_approval/general/enabled';

    /**
     * @var string
     */
    public const XML_PATH_CUSTOMER_SHOW_MENU_BADGE = 'customer_approval/general/show_badge';

    /**
     * @var string
     */
    public const XML_PATH_CUSTOMER_APPROVAL_NOTIFY_ENABLED = 'customer_approval/notifications/enabled';

    /**
     * @var string
     */
    public const XML_PATH_CUSTOMER_APPROVAL_NOTIFY_EMAIL_TEMPLATE = 'customer_approval/notifications/email_template';

    /**
     * @var string
     */
    public const XML_PATH_CUSTOMER_APPROVAL_NOTIFY_EMAIL_IDENTITY = 'customer_approval/notifications/email_identity';

    /**
     * @var string
     */
    public const XML_PATH_CUSTOMER_APPROVAL_LOGIN_ERROR_MESSAGE = 'customer_approval/general/error_message';

    /**
     * @var string
     */
    public const XML_PATH_CUSTOMER_APPROVAL_CREATE_ACCOUNT_MESSAGE = 'customer_approval/general/create_account_message';

    /**
     * @var string
     */
    public const XML_PATH_CUSTOMER_APPROVAL_CREATE_ACCOUNT_REPLACE_MESSAGE
        = 'customer_approval/general/create_account_replace_message';

    /**
     * @var string
     */
    public const ACL_APPROVE_ACTION = 'Halex_CustomerApproval::approve_customer';

    /**
     * @var string
     */
    public const ACL_UNAPPROVE_ACTION = 'Halex_CustomerApproval::unapprove_customer';

    /**
     * @var string
     */
    public const IS_APPROVED_ATTR_CODE = 'is_approved';

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Is Enabled Module
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->getValue(
                self::XML_PATH_CUSTOMER_APPROVAL_ENABLED,
                ScopeInterface::SCOPE_STORE
            ) === '1';
    }

    /**
     * Is Enabled Notifications
     *
     * @return bool
     */
    public function isNotifyEnabled(): bool
    {
        return $this->scopeConfig->getValue(
                self::XML_PATH_CUSTOMER_APPROVAL_NOTIFY_ENABLED,
                ScopeInterface::SCOPE_STORE
            ) === '1';
    }

    /**
     * Get Notify Email Template
     *
     * @return bool
     */
    public function getNotifyEmailTemplate(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_APPROVAL_NOTIFY_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Login Error Message
     *
     * @return string|null
     */
    public function getLoginErrorMessage(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_APPROVAL_LOGIN_ERROR_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Create Account Message
     *
     * @return string|null
     */
    public function getCreateAccountMessage(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_APPROVAL_CREATE_ACCOUNT_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Sucess Message Replacement Enabled (Customer Creation)
     *
     * @return bool
     */
    public function isSuccessMessageReplacementEnabled(): bool
    {
        return $this->scopeConfig->getValue(
                self::XML_PATH_CUSTOMER_APPROVAL_CREATE_ACCOUNT_REPLACE_MESSAGE,
                ScopeInterface::SCOPE_STORE
            ) === '1';
    }

    /**
     * Is Enabled Module
     *
     * @return bool
     */
    public function isMenuBadgeEnabled(): bool
    {
        return $this->scopeConfig->getValue(
                self::XML_PATH_CUSTOMER_SHOW_MENU_BADGE,
                ScopeInterface::SCOPE_STORE
            ) === '1';
    }

    /**
     * Get Email Identity
     *
     * @return string|null
     */
    public function getEmailIdentity(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_APPROVAL_NOTIFY_EMAIL_IDENTITY,
            ScopeInterface::SCOPE_STORE
        );
    }
}
