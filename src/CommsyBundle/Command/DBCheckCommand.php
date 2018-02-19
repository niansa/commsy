<?php
namespace CommsyBundle\Command;

use CommsyBundle\Database\DatabaseChecks;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DBCheckCommand extends Command
{
    private $databaseChecks;

    public function __construct(DatabaseChecks $databaseChecks)
    {
        $this->databaseChecks = $databaseChecks;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('commsy:db:check')
            ->setDescription('Checks the database tables')
            ->addOption(
                'fix',
                null,
                InputOption::VALUE_NONE,
                'Try to fix found errors'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelper('formatter');

        
        $output->writeln('<info>foo</info>');

        $this->databaseChecks->runChecks();
    }
}