<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Ui\Component\Pending;

use Halex\CustomerApproval\Model\Config;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as SourceDataProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;

class DataProvider extends SourceDataProvider
{
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * Constructor
     *
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CustomerRepositoryInterface $customerRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->customerRepository = $customerRepository;
    }

    /**
     * Search Result To Output
     *
     * @param SearchResultInterface $searchResult
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [
            'items' => [],
        ];

        foreach ($searchResult->getItems() as $item) {
            $itemData = [];
            foreach ($item->getCustomAttributes() as $attribute) {
                $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
            }

            if (!isset($itemData[Config::IS_APPROVED_ATTR_CODE])) {
                $customerEntity = $this->customerRepository->getById($itemData['entity_id']);
                $itemData[Config::IS_APPROVED_ATTR_CODE] = $customerEntity->getCustomAttribute(
                    Config::IS_APPROVED_ATTR_CODE
                )->getValue();
            }

            $arrItems['items'][] = $itemData;
        }

        $arrItems['items'] = array_values(
            array_filter($arrItems['items'], function ($item) {
                return $item[Config::IS_APPROVED_ATTR_CODE] !== '1';
            })
        );

        $arrItems['totalRecords'] = count($arrItems['items']);

        return $arrItems;
    }

    /**
     * Get Data
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getData()
    {
        return $this->searchResultToOutput($this->getSearchResult());
    }
}
