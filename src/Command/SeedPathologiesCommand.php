<?php

namespace App\Command;

use App\Entity\Pathologie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:seed:pathologies', description: 'Seed default pathologies if they do not exist')]
final class SeedPathologiesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $defaults = [
            'Epilepsie',
            'AVC',
            'Migraine',
            'Sclérose en plaques',
            'Maladie de Parkinson',
            'Alzheimer',
            'Traumatisme crânien',
            'Tumeur cérébrale',
            'Infection neurologique',
            'Trouble du sommeil',
            'Douleur chronique',
            'Neuropathie périphérique',
            'Maladie neuromusculaire',
            'Trouble du mouvement',
            'Maladie vasculaire cérébrale',
        ];

        $repo = $this->em->getRepository(Pathologie::class);

        foreach ($defaults as $libelle) {
            $existing = $repo->findOneBy(['libelle' => $libelle]);
            if (!$existing) {
                $p = new Pathologie();
                $p->setLibelle($libelle);
                $this->em->persist($p);
                $output->writeln(sprintf('Inserted: %s', $libelle));
            } else {
                $output->writeln(sprintf('Exists: %s', $libelle));
            }
        }

        $this->em->flush();

        $output->writeln('Done.');

        return Command::SUCCESS;
    }
}
