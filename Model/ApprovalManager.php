<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ApprovalManager
 */
class ApprovalManager
{
    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var StateInterface
     */
    private StateInterface $inlineTranslation;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var Config
     */
    private Config $customerApprovalConfig;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $customerCollectionFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * Constructor
     *
     * @param TransportBuilder $transportBuilder
     * @param Config $customerApprovalConfig
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $state
     * @param CollectionFactory $customerCollectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        Config $customerApprovalConfig,
        StoreManagerInterface $storeManager,
        StateInterface $state,
        CollectionFactory $customerCollectionFactory,
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->customerApprovalConfig = $customerApprovalConfig;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

    /**
     * Send Email
     *
     * @param string $sendTo
     * @param string $customerName
     * @param int|string $storeId
     */
    public function sendEmail(
        string $sendTo,
        string $customerName,
        int|string $storeId
    ): void {
        try {
            $this->inlineTranslation->suspend();

            $currentStore = $this->storeManager->getStore($storeId);
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->customerApprovalConfig->getNotifyEmailTemplate())
                ->setTemplateOptions(
                    [
                        'area' => 'frontend',
                        'store' => Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'customerEmail' => $sendTo,
                    'customerName' => $customerName,
                    'storeName' => $currentStore->getName(),
                ])
                ->setFromByScope($this->customerApprovalConfig->getEmailIdentity())
                ->addTo($sendTo)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * Get Pending Customer Count
     *
     * @return int
     */
    public function getPendingCustomersCount(): int
    {
        try {
            $customerCollection = $this->customerCollectionFactory->create();
            $customerCollection->addAttributeToSelect('is_approved')
                ->addAttributeToFilter('is_approved', ['neq' => 1]);

            return $customerCollection->getSize();
        } catch (LocalizedException) {
            return 0;
        }
    }

    /**
     * Is Customer Approved
     *
     * @param int|string $customerId
     *
     * @return bool
     */
    public function isCustomerApproved(int|string $customerId): bool
    {
        try {
            $customerEntity = $this->customerRepository->getById($customerId);

            return $customerEntity->getCustomAttribute('is_approved')->getValue() === '1';
        } catch (NoSuchEntityException | LocalizedException) {
            return false;
        }
    }
}
