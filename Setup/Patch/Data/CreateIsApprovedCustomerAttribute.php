<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Setup\Patch\Data;

use Exception;
use Halex\CustomerApproval\Model\Config;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Psr\Log\LoggerInterface;

/**
 * Class CreateIsApprovedCustomerAttribute
 */
class CreateIsApprovedCustomerAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var CustomerSetup
     */
    private CustomerSetup $customerSetup;

    /**
     * @var AttributeResource
     */
    private AttributeResource $attributeResource;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeResource $attributeResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeResource $attributeResource,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetup = $customerSetupFactory->create(['setup' => $moduleDataSetup]);
        $this->attributeResource = $attributeResource;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Apply Method
     *
     * @return void
     */
    public function apply()
    {
        try {
            $this->moduleDataSetup->startSetup();

            $this->customerSetup->addAttribute(
                CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                Config::IS_APPROVED_ATTR_CODE,
                [
                    'label' => 'Is Approved',
                    'type' => 'int',
                    'input' => 'boolean',
                    'required' => false,
                    'position' => 200,
                    'system' => false,
                    'default' => 0,
                    'user_defined' => true,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => true,
                    'is_filterable_in_grid' => true,
                    'is_searchable_in_grid' => true,
                ]
            );

            $this->customerSetup->addAttributeToSet(
                CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
                null,
                Config::IS_APPROVED_ATTR_CODE
            );

            $attribute = $this->customerSetup->getEavConfig()
                ->getAttribute(
                    CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                    Config::IS_APPROVED_ATTR_CODE
                );

            $attribute->setData(
                'used_in_forms',
                [
                    'adminhtml_customer',
                ]
            );

            $this->attributeResource->save($attribute);

            $this->moduleDataSetup->endSetup();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }
}
