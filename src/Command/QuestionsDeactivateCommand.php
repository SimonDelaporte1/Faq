<?php

namespace App\Command;

use App\Repository\QuestionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QuestionsDeactivateCommand extends Command
{
    protected static $defaultName = 'app:questions:deactivate';
    protected static $defaultDescription = 'Update active field in question table';

    private $QuestionRepository;
    private $entityManager;

    public function __construct(QuestionRepository $QuestionRepository, ManagerRegistry $doctrine)
    {
        $this->questionRepository = $QuestionRepository;
        $this->entityManager = $doctrine->getManager();

        // On appelle le constructeur parent
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('nbJour', InputArgument::OPTIONAL, 'Ancienneté de la dernière réponse')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $nbJour = $input->getArgument('nbJour');
        if (empty($nbJour)) {
            $nbJour = 7;
        }

        $io->info('Mise à jour des dates : ' . $nbJour );

        $this->questionRepository->updateActivatedAd($nbJour);

        $io->success('Mise à jour des dates effectuée');

        return Command::SUCCESS;
    }
}
