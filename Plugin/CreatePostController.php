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
use Magento\Customer\Controller\Account\CreatePost;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CreatePostController
 */
class CreatePostController
{
    /**
     * @var MessageManagerInterface
     */
    protected MessageManagerInterface $messageManager;

    /**
     * @var Config
     */
    private Config $customerApprovalConfig;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * Create Post Controller Constructor
     *
     * @param MessageManagerInterface $messageManager
     * @param Config $customerApprovalConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        MessageManagerInterface $messageManager,
        Config $customerApprovalConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->messageManager = $messageManager;
        $this->customerApprovalConfig = $customerApprovalConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * After Execute
     *
     * @param CreatePost $controller
     * @param $controllerResult
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function afterExecute(CreatePost $controller, $controllerResult)
    {
        if (!$this->customerApprovalConfig->isEnabled()) {
            return $controllerResult;
        }

        if ($this->messageManager->getMessages()->getCountByType('success') > 0) {
            if ($this->customerApprovalConfig->isSuccessMessageReplacementEnabled()) {
                $this->messageManager->getMessages(true);
            }

            $this->messageManager->addSuccessMessage(
                __(
                    $this->customerApprovalConfig->getCreateAccountMessage()
                        ?: 'Thank you for registering with %1. Your account added for review, you will get an email when it is approved.',
                    $this->storeManager->getStore()->getFrontendName()
                )
            );
        }

        return $controllerResult;
    }
}
