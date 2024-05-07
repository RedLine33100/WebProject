<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507002138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pays_id INTEGER NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, CONSTRAINT FK_7D3656A4A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7D3656A4A6E44244 ON account (pays_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON account (username)');
        $this->addSql('CREATE TABLE cart (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, id_account INTEGER NOT NULL, is_paid BOOLEAN NOT NULL, CONSTRAINT FK_BA388B7A3ABFFD4 FOREIGN KEY (id_account) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BA388B7A3ABFFD4 ON cart (id_account)');
        $this->addSql('CREATE TABLE pays (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, short_name VARCHAR(10) NOT NULL, name VARCHAR(100) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_349F3CAE3EE4B093 ON pays (short_name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_349F3CAE5E237E06 ON pays (name)');
        $this->addSql('CREATE TABLE produit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, number INTEGER NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_29A5EC275E237E06 ON produit (name)');
        $this->addSql('CREATE TABLE produit_pays (produit_id INTEGER NOT NULL, pays_id INTEGER NOT NULL, PRIMARY KEY(produit_id, pays_id), CONSTRAINT FK_1D074141F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1D074141A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_1D074141F347EFB ON produit_pays (produit_id)');
        $this->addSql('CREATE INDEX IDX_1D074141A6E44244 ON produit_pays (pays_id)');
        $this->addSql('CREATE TABLE produit_cart (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, produit INTEGER NOT NULL, pays INTEGER NOT NULL, cart INTEGER NOT NULL, amount INTEGER NOT NULL, CONSTRAINT FK_223BF55829A5EC27 FOREIGN KEY (produit) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_223BF558349F3CAE FOREIGN KEY (pays) REFERENCES pays (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_223BF558BA388B7 FOREIGN KEY (cart) REFERENCES cart (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_223BF55829A5EC27 ON produit_cart (produit)');
        $this->addSql('CREATE INDEX IDX_223BF558349F3CAE ON produit_cart (pays)');
        $this->addSql('CREATE INDEX IDX_223BF558BA388B7 ON produit_cart (cart)');
        $this->addSql('CREATE UNIQUE INDEX unique_triplet ON produit_cart (produit, pays, cart)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE pays');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE produit_pays');
        $this->addSql('DROP TABLE produit_cart');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
