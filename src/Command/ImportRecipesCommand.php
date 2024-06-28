<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:import-recipes',description: 'Export code table to a CSV file')]
class ImportRecipesCommand extends Command
{
    protected static $defaultName = 'app:import-recipes';
    private $entityManager;
    private Connection $connection;
    private string $projectDir;

    public function __construct(EntityManagerInterface $entityManager, Connection $connection, string $projectDir)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->connection = $connection;
        $this->projectDir = $projectDir;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import recipes from a CSV file into the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $csvFile = $this->projectDir . '/var/data/recipes.csv';

        if (!file_exists($csvFile) || !is_readable($csvFile)) {
            $io->error('CSV file does not exist or is not readable.');
            return Command::FAILURE;
        }

        $this->connection->executeStatement('TRUNCATE TABLE recipe');

        if (($handle = fopen($csvFile, 'r')) !== false) {
            $header = fgetcsv($handle);

            $batchSize = 20;
            $batch = [];

            $this->connection->beginTransaction();
            try {
                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row) !== count($header)) {
                        $io->warning('Skipping row due to column mismatch: ' . json_encode($row));
                        continue;
                    }

                    $row = array_combine($header, $row);

                    // Preprocess fields if necessary
                    $row['ingredients'] = json_encode(json_decode($row['ingredients'], true));
                    $row['directions'] = json_encode(json_decode($row['directions'], true));
                    $row['ner'] = json_encode(json_decode($row['ner'], true));

                    $batch[] = [
                        'userId' => 0,
                        'title' => $row['title'],
                        'ingredients' => $row['ingredients'],
                        'directions' => $row['directions'],
                        'link' => $row['link'],
                        'source' => $row['source'],
                        'ner' => $row['ner'],
                        'site' => $row['site'],
                    ];

                    if (count($batch) >= $batchSize) {
                        $this->insertBatch($batch);
                        $batch = [];
                    }
                }

                if (!empty($batch)) {
                    $this->insertBatch($batch);
                }

                $this->connection->commit();
                $io->success('Recipes imported successfully!');
                return Command::SUCCESS;
            } catch (\Exception $e) {
                $this->connection->rollBack();
                $io->error('Failed to import recipes: ' . $e->getMessage());
                return Command::FAILURE;
            } finally {
                fclose($handle);
            }
        } else {
            $io->error('Unable to open CSV file.');
            return Command::FAILURE;
        }
    }

    private function insertBatch(array $batch): void
    {
        $sql = 'INSERT INTO recipe (user_id, title, ingredients, directions, link, source, ner, site) VALUES ';
        $values = [];

        foreach ($batch as $row) {
            $values[] = sprintf(
                "(%s, %s, %s, %s, %s, %s, %s, %s)",
                $this->connection->quote($row['userId']),
                $this->connection->quote($row['title']),
                $this->connection->quote($row['ingredients']),
                $this->connection->quote($row['directions']),
                $this->connection->quote($row['link']),
                $this->connection->quote($row['source']),
                $this->connection->quote($row['ner']),
                $this->connection->quote($row['site'])
            );
        }

        $sql .= implode(', ', $values);
        $this->connection->executeQuery($sql);
    }

}


