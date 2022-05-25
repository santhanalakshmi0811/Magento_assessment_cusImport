<?php

namespace CustomerImp\CustomerCreation\Model;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\InvalidArgumentException;
use CustomerImp\CustomerCreation\Model\Customer;

class JsonImport
{
    private Customer $customer;
    public function __construct(
        Customer $customer
    ) {
        $this->customer = $customer;
    }

    /**
     * @throws FileSystemException
     * @throws InvalidArgumentException
     */
    public function process(string $filepath): void
    {
        if (!file_exists($filepath)) {
            throw new FileSystemException(__('Json file not found'));
        }
        $str = file_get_contents($filepath);
        $json = json_decode($str, true);
        if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(__('Not a valid json file'));
        }
        foreach ($json as $value) {
            $data = [];
            foreach ($value as $key => $val) {
                $data[$key] = $val;
            }
            $this->customer->createCustomer($data);
        }
    }
}
