<?php

namespace CustomerImp\CustomerCreation\Model\Import;

use Magento\CustomerImportExport\Model\Import\Customer;

class CustomerImport extends Customer
{
    public function importCustomerData(array $rowData)
    {
        $this->prepareCustomerData($rowData);
        $entitiesToCreate = [];
        $entitiesToUpdate = [];

        $processedData = $this->_prepareDataForUpdate($rowData);
        $entitiesToCreate = array_merge($entitiesToCreate, $processedData[self::ENTITIES_TO_CREATE_KEY]);

        /**
         * Save prepared data
         */
        if ($entitiesToCreate) {
            $this->_saveCustomerEntities($entitiesToCreate, $entitiesToUpdate);
        }
        return $entitiesToCreate[0]['entity_id'] ?? null;
    }
}
