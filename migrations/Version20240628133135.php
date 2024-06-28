<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240628133135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create recipe table with auto-incrementing id';
    }

    public function up(Schema $schema): void
    {
        // Add SQL to create the sequence if it does not exist
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS recipe_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        // Add SQL to create the table
        $this->addSql('CREATE TABLE IF NOT EXISTS recipe (
            id INT NOT NULL DEFAULT nextval(\'recipe_id_seq\'),
            title VARCHAR(255) NOT NULL,
            ingredients TEXT NOT NULL,
            directions TEXT NOT NULL,
            link VARCHAR(255) DEFAULT NULL,
            source VARCHAR(255) DEFAULT NULL,
            ner VARCHAR(255) DEFAULT NULL,
            site VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY(id)
        )');
    }

    public function down(Schema $schema): void
    {
        // Add SQL to drop the table and sequence
        $this->addSql('DROP TABLE IF EXISTS recipe');
        $this->addSql('DROP SEQUENCE IF EXISTS recipe_id_seq');
    }
}
