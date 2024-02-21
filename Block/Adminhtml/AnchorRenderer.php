<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Block\Adminhtml;

use Halex\CustomerApproval\Model\ApprovalManager;
use Halex\CustomerApproval\Model\Config;
use Magento\Backend\Block\AnchorRenderer as SourceAnchorRenderer;
use Magento\Backend\Block\MenuItemChecker;
use Magento\Backend\Model\Menu\Item;
use Magento\Framework\Escaper;

/**
 * Class AnchorRenderer
 */
class AnchorRenderer extends SourceAnchorRenderer
{
    /**
     * @var MenuItemChecker
     */
    private MenuItemChecker $menuItemChecker;

    /**
     * @var Escaper
     */
    private Escaper $escaper;

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
     * @param MenuItemChecker $menuItemChecker
     * @param Escaper $escaper
     * @param Config $customerApprovalConfig
     * @param ApprovalManager $customerApprovalManager
     */
    public function __construct(
        MenuItemChecker $menuItemChecker,
        Escaper $escaper,
        Config $customerApprovalConfig,
        ApprovalManager $customerApprovalManager
    ) {
        parent::__construct(
            $menuItemChecker,
            $escaper
        );

        $this->menuItemChecker = $menuItemChecker;
        $this->escaper = $escaper;
        $this->customerApprovalConfig = $customerApprovalConfig;
        $this->customerApprovalManager = $customerApprovalManager;
    }

    /**
     * Render Pending Customers Badge
     *
     * @return string
     */
    private function renderPendingCustomersBadge(): string
    {
        if (!$this->customerApprovalConfig->isMenuBadgeEnabled()) {
            return '';
        }

        $pendingCustomersCount = $this->customerApprovalManager->getPendingCustomersCount();

        return '<span class="customer-pending-badge ' . ($pendingCustomersCount ? 'not_empty' : '') . '">'
            . $pendingCustomersCount
            . '</span>';
    }

    /**
     * Render Anchor
     *
     * @param false|Item $activeItem
     * @param Item $menuItem
     * @param int $level
     *
     * @return string
     */
    public function renderAnchor(
        $activeItem,
        Item $menuItem,
        $level
    ): string {
        if ($menuItem->getId() === 'Halex_CustomerApproval::pending_customers') {
            $target = $menuItem->getTarget() ? ('target=' . $menuItem->getTarget()) : '';
            return '<a href="' . $menuItem->getUrl() . '" ' . $target . ' ' . $this->_renderItemAnchorTitle(
                    $menuItem
                ) . $this->_renderItemOnclickFunction(
                    $menuItem
                ) . ' class="customer-pending-anchor ' . ($this->menuItemChecker->isItemActive(
                    $activeItem,
                    $menuItem,
                    $level
                ) ? '_active' : '')
                . '">' . '<span>' . $this->escaper->escapeHtml(__($menuItem->getTitle()))
                . '</span>' . $this->renderPendingCustomersBadge() . '</a>';
        }

        return parent::renderAnchor($activeItem, $menuItem, $level);
    }

    /**
     * Render menu item anchor title
     *
     * @param Item $menuItem
     *
     * @return string
     */
    private function _renderItemAnchorTitle($menuItem): string
    {
        return $menuItem->hasTooltip() ? 'title="' . __($menuItem->getTooltip()) . '"' : '';
    }

    /**
     * Render menu item onclick function
     *
     * @param Item $menuItem
     *
     * @return string
     */
    private function _renderItemOnclickFunction($menuItem): string
    {
        return $menuItem->hasClickCallback() ? ' onclick="' . $menuItem->getClickCallback() . '"' : '';
    }
}
