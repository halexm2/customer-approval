<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Ui\Component\MassAction\Group;

use Halex\CustomerApproval\Model\Config;
use JsonSerializable;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Options
 */
class Options implements JsonSerializable
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var AuthorizationInterface
     */
    private AuthorizationInterface $authorization;

    /**
     * Constructor
     *
     * @param UrlInterface $urlBuilder
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        UrlInterface $urlBuilder,
        AuthorizationInterface $authorization
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->authorization = $authorization;
    }

    /**
     * Get Approve Action
     *
     * @return array
     */
    private function getApproveAction(): array
    {
        if (!$this->authorization->isAllowed(Config::ACL_APPROVE_ACTION)) {
            return [];
        }

        return [
            'type' => 'approve',
            'label' => __('Approve'),
            '__disableTmpl' => true,
            'url' => $this->getMassActionUrl(1),
            'confirm' => [
                'title' => 'Approve Customers',
                'message' => 'Are you sure to approve selected customers?',
            ],
        ];
    }

    /**
     * Get Unapprove Action
     *
     * @return array
     */
    private function getUnapproveAction(): array
    {
        if (!$this->authorization->isAllowed(Config::ACL_UNAPPROVE_ACTION) || $this->isCustomersPendingGrid()) {
            return [];
        }

        return [
            'type' => 'unapprove',
            'label' => __('Unapprove'),
            '__disableTmpl' => true,
            'url' => $this->getMassActionUrl(0),
            'confirm' => [
                'title' => 'Unapprove Customers',
                'message' => 'Are you sure to unapprove selected customers? Selected customers wouldn\'t be able to login.',
            ],
        ];
    }

    /**
     * Is Customer Pending Grid
     *
     * @return bool
     */
    private function isCustomersPendingGrid(): bool
    {
        return str_contains($this->urlBuilder->getUrl(), 'customer_approval');
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $actionsData = [];

        if (count($this->getApproveAction())) {
            $actionsData[] = $this->getApproveAction();
        }

        if (count($this->getUnapproveAction())) {
            $actionsData[] = $this->getUnapproveAction();
        }

        return $actionsData;
    }

    /**
     * Get Mass Action URL
     *
     * @param int $approvedStatus
     *
     * @return string
     */
    private function getMassActionUrl(int $approvedStatus): string
    {
        return $this->urlBuilder->getUrl(
            'customer_approval/mass/update',
            ['is_approved' => $approvedStatus]
        );
    }
}
