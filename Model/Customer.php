<?php

namespace CustomerImp\CustomerCreation\Model;

use Exception;
use Magento\Store\Model\StoreManagerInterface;
use CustomerImp\CustomerCreation\Model\Import\CustomerImport;
use Symfony\Component\Console\Output\OutputInterface;

class Customer
{
    private StoreManagerInterface $storeManagerInterface;
    private CustomerImport $customerImport;
    public OutputInterface $output;

    public function __construct(
        StoreManagerInterface $storeManagerInterface,
        CustomerImport $customerImport
    ) {
        $this->storeManagerInterface = $storeManagerInterface;
        $this->customerImport = $customerImport;
    }



    public function createCustomer(array $data): void
    {
        try {
            // collect the customer data

            // get store and website ID
            $store = $this->storeManagerInterface->getStore();
            $websiteId = $this->storeManagerInterface->getWebsite()->getId();
            $storeId = $store->getId();

            $customerData = [
                'email'         => $data['emailaddress'],
                '_website'      => 'base',
                '_store'        => 'default',
                'confirmation'  => null,
                'dob'           => null,
                'firstname'     => $data['fname'],
                'gender'        => null,
                'lastname'      => $data['lname'],
                'middlename'    => null,
                'password_hash' => null,
                'prefix'        => null,
                'store_id'      => $storeId,
                'website_id'    => $websiteId,
                'password'      => null,
                'disable_auto_group_change' => 0
            ];

            // save the customer data
            $this->customerImport->importCustomerData($customerData);
        } catch (Exception $e) {
            $this->output->writeln(
                '<error>'. $e->getMessage() .'</error>',
                OutputInterface::OUTPUT_NORMAL
            );
        }
    }
}
