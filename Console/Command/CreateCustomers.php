<?php

namespace CustomerImp\CustomerCreation\Console\Command;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use CustomerImp\CustomerCreation\Model\CsvImport;
use CustomerImp\CustomerCreation\Model\JsonImport;

class CreateCustomers extends Command
{
    private Filesystem $filesystem;
    private CsvImport $csvimport;
    private JsonImport $jsonimport;
    private State $state;
    const PROFILE = 'profile';
    const SOURCE = 'source';

    public function __construct(
        Filesystem $filesystem,
        CsvImport $csvimport,
        JsonImport $jsonimport,
        State $state
    ) {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->csvimport = $csvimport;
        $this->jsonimport = $jsonimport;
        $this->state = $state;
    }

    public function configure(): void
    {
        $options = [
            new InputOption(
                self::PROFILE,
                null,
                InputOption::VALUE_REQUIRED,
                'type'
            ),
            new InputArgument(
                self::SOURCE,
                null,
                InputArgument::REQUIRED,
                'source'
            ),
        ];

        $this->setName('customer:importer')
            ->setDescription('Import customer via command')
            ->setDefinition($options);
        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        try {
            $this->state->setAreaCode(Area::AREA_GLOBAL);
            $type = $input->getOption(self::PROFILE);
            $filename = $input->getArgument(self::SOURCE);
            $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $filepath = $mediaDir->getAbsolutePath() . 'import/'.$filename;
            $ext = pathinfo($filepath, PATHINFO_EXTENSION);
            if ($ext!=$type) {
                throw new InvalidArgumentException(__('File type not match'));
            }
            if ($type=='json') {
                $this->jsonimport->process($filepath);
            }
            if ($type=='csv') {
                $this->csvimport->process($filepath);
            }
            return Cli::RETURN_SUCCESS;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $output->writeln("<error>$msg</error>", OutputInterface::OUTPUT_NORMAL);
            return Cli::RETURN_FAILURE;
        }
    }
}
