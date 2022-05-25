<?php

namespace CustomerImp\CustomerCreation\Model;

use Generator;
use Magento\Framework\Exception\FileSystemException;
use CustomerImp\CustomerCreation\Model\Customer;

class CsvImport
{
    private Customer $customer;

    public function __construct(
        Customer $customer
    ) {
        $this->customer = $customer;
    }

    public function process(string $filepath): void
    {
        if (!file_exists($filepath)) {
            throw new FileSystemException(__('CSV file not found'));
        }

        // read the csv header
        $header = $this->readCsvHeader($filepath)->current();

        // read the csv file and skip the first (header) row
        $row = $this->readCsvRows($filepath, $header);
        $row->next();

        // while the generator is open, read current row data, create a customer and resume the generator
        while ($row->valid()) {
            $data = $row->current();
            $this->customer->createCustomer($data);
            $row->next();
        }
    }

    private function readCsvRows(string $file, array $header): ?Generator
    {
        $handle = fopen($file, 'rb');

        while (!feof($handle)) {
            $data = [];
            $rowData = fgetcsv($handle);
            if ($rowData) {
                foreach ($rowData as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                yield $data;
            }
        }

        fclose($handle);
    }

    private function readCsvHeader(string $file): ?Generator
    {
        $handle = fopen($file, 'rb');

        while (!feof($handle)) {
            yield fgetcsv($handle);
        }

        fclose($handle);
    }
}
