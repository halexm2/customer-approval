<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Controller\Adminhtml\Pending;

use Halex\CustomerApproval\Model\ApprovalManager;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class Grid
 */
class Grid extends Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;

    /**
     * @var ApprovalManager
     */
    private ApprovalManager $customerApprovalManager;

    /**
     * ACL
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Halex_CustomerApproval::pending_customers';

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $rawFactory
     * @param ApprovalManager $customerApprovalManager
     */
    public function __construct(
        Context $context,
        PageFactory $rawFactory,
        ApprovalManager $customerApprovalManager
    ) {
        parent::__construct($context);

        $this->pageFactory = $rawFactory;
        $this->customerApprovalManager = $customerApprovalManager;
    }

    /**
     * Add the main Admin Grid page
     *
     * @return Page
     */
    public function execute(): Page
    {
        $resultPage = $this->pageFactory->create();

        $resultPage->setActiveMenu('Halex_CustomerApproval::pending_customers')
            ->getConfig()
            ->getTitle()
            ->prepend(__('Pending Customers (%1)', $this->customerApprovalManager->getPendingCustomersCount()));

        return $resultPage;
    }
}
