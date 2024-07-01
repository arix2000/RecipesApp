<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    const BATCH_SIZE = 20;
    private string $projectDir;
    private array $images;
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;
    private int $insertedRows = 0;
    private User $user;

    public function __construct(string $projectDir, EntityManagerInterface $entityManager)
    {
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager;
        $this->client = HttpClient::create();
    }

    public function load(ObjectManager $manager): void
    {
        ini_set('memory_limit', '1536M');
        $this->getImages();
        $csvFile = $this->projectDir . '/var/data/recipes.csv';

        if (!file_exists($csvFile) || !is_readable($csvFile)) {
            throw new \Exception('CSV file does not exist or is not readable.');
        }

        $this->user = $this->getReference('user_reference');

        if (($handle = fopen($csvFile, 'r')) !== false) {
            $header = fgetcsv($handle);
            $batch = [];

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) !== count($header)) {
                    continue;
                }

                $row = array_combine($header, $row);

                $row['ingredients'] = json_encode(json_decode($row['ingredients'], true));
                $row['directions'] = json_encode(json_decode($row['directions'], true));
                $row['ner'] = json_encode(json_decode($row['ner'], true));

                $batch[] = [
                    'user' => $this->user,
                    'title' => $row['title'],
                    'ingredients' => $row['ingredients'],
                    'directions' => $row['directions'],
                    'link' => "https://" . $row['link'],
                    'source' => $row['source'],
                    'ner' => $row['ner'],
                    'site' => "https://" . $row['site'],
                    'imageUrl' => $this->getRandomImage()
                ];

                if (count($batch) >= self::BATCH_SIZE) {
                    $this->insertBatch($manager, $batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                $this->insertBatch($manager, $batch);
            }

            fclose($handle);
        } else {
            throw new \Exception('Unable to open CSV file.');
        }
    }

    private function insertBatch(ObjectManager $manager, array $batch): void
    {
        foreach ($batch as $row) {
            $recipe = Recipe::createFrom(
                $row['user'],
                $row['title'],
                $row['ingredients'],
                $row['directions'],
                $row['link'],
                $row['source'],
                $row['ner'],
                $row['site'],
                $row['imageUrl']
            );

            $manager->persist($recipe);
            printf("\33[2K\r");
            printf("\033[32m    [INFO] Inserting rows: %d / 99592\033[0m", $this->insertedRows);
            flush();
            $this->insertedRows++;
        }

        $manager->flush();
        $manager->clear();
        $this->user = $this->getReference('user_reference');
    }

    private function getRandomImage(): string
    {
        shuffle($this->images);
        $randomId = array_rand($this->images);
        return $this->images[$randomId];
    }

    private function getImages(): void
    {
        $images = array();
        for ($i = 1; $i <= 300; $i++) {
            $images[$i] = $this->getImage();
            printf("\33[2K\r");
            printf("\033[32m    [INFO] Loading images: %d / 300\033[0m", $i);
            flush();
        }
        printf("\n");
        $this->images = $images;
    }

    private function getImage(): ?string
    {
        $url = 'https://foodish-api.com/api/';

        try {
            $response = $this->client->request('GET', $url);
            $statusCode = $response->getStatusCode();
            $content = $response->getContent();

            if ($statusCode === 200) {
                $data = json_decode($content, true);
                return (string)$data["image"];
            } else {
                throw new Exception("Failed to fetch data. Status code: $statusCode");
            }
        } catch (Exception $e) {
            throw new Exception('An error occurred: ' . $e->getMessage());
        }
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}

