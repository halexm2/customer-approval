<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Ui\Component\Listing\Column;

use Halex\CustomerApproval\Model\Config;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\AuthorizationInterface;

/**
 * Class Actions
 */
class Actions extends Column
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
     * Url path
     */
    public const URL_PATH_SHOW_CUSTOMER = 'customer/index/edit';

    /**
     * Url path
     */
    public const URL_PATH_APPROVE_CUSTOMER = 'customer_approval/action/approve';

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param AuthorizationInterface $authorization
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );

        $this->urlBuilder = $urlBuilder;
        $this->authorization = $authorization;
    }

    /**
     * Get Approve Action
     *
     * @return array[]
     */
    private function getApproveAction(int|string $customerEntityId): array
    {
        if (!$this->authorization->isAllowed(Config::ACL_APPROVE_ACTION)) {
            return [];
        }

        return [
            'approve' => [
                'href' => $this->urlBuilder->getUrl(
                    static::URL_PATH_APPROVE_CUSTOMER,
                    [
                        'entity_id' => $customerEntityId,
                    ]
                ),
                'label' => __('Approve'),
            ],
        ];
    }

    /**
     * Get Show Customer Action
     *
     * @return array[]
     */
    private function getShowCustomerAction(int|string $customerEntityId): array
    {
        return [
            'show_customer' => [
                'href' => $this->urlBuilder->getUrl(
                    static::URL_PATH_SHOW_CUSTOMER,
                    [
                        'id' => $customerEntityId,
                    ]
                ),
                'label' => __('Show Customer'),
            ],
        ];
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
                    $item[$this->getData('name')] = [
                        ...$this->getApproveAction($item['entity_id']),
                        ...$this->getShowCustomerAction($item['entity_id']),
                    ];
                }
            }
        }

        return $dataSource;
    }
}
