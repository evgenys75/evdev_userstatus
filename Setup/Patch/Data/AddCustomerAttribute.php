<?php

namespace EvDev\UserStatus\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Setup\Patch\Data\UpdateIdentifierCustomerAttributesVisibility;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Model\ResourceModel\Attribute;

/**
 * Class AddCustomerAttribute
 * @package EvDev\UserStatus\Setup\Patch\Data
 */
class AddCustomerAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var CustomerSetupFactory
     */
    private CustomerSetupFactory $customerSetupFactory;

    /**
     * @var Attribute
     */
    private Attribute $attributeResource;
    /**
     * @var AttributeSetFactory
     */
    private AttributeSetFactory $attributeSetFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param Attribute $attributeResource
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        Attribute $attributeResource,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeResource = $attributeResource;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        /**
         * Add attribute
         */
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'customer_status',
            [
                'type' => 'int',
                'label' => 'Customer Status',
                'input' => 'boolean',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'position' => 999,
                'system' => 0,
            ]
        );


        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);


        $customerSetup->addAttributeToGroup(Customer::ENTITY, $attributeSetId, $attributeGroupId, 'customer_status');

        $customerAttribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'customer_status');

        $customerAttribute->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_edit',
        ]);

        $this->attributeResource->save($customerAttribute);


        $this->moduleDataSetup->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            UpdateIdentifierCustomerAttributesVisibility::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return string
     */
    public static function getVersion()
    {
        return '1.0.9';
    }
}
