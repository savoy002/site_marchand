<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200324101833 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, adr_id_user INT DEFAULT NULL, username VARCHAR(180) NOT NULL, /*roles JSON NOT NULL*/ roles TEXT NOT NULL COMMENT \'(DC2Type:json_array)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at_user DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, valid_user TINYINT(1) DEFAULT \'0\' NOT NULL, deleted_user TINYINT(1) DEFAULT \'0\' NOT NULL, admin_user TINYINT(1) DEFAULT \'0\' NOT NULL, super_admin TINYINT(1) DEFAULT \'0\' NOT NULL, img VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_2DA17977F85E0677 (username), INDEX IDX_2DA17977EB5D4312 (adr_id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Comment (id INT AUTO_INCREMENT NOT NULL, user_id_comment INT DEFAULT NULL, prod_id_comment INT DEFAULT NULL, mark_comment INT NOT NULL, text_comment LONGTEXT NOT NULL, INDEX IDX_5BC96BF0ED8E3DF0 (user_id_comment), INDEX IDX_5BC96BF09A7C4C37 (prod_id_comment), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Command (id INT AUTO_INCREMENT NOT NULL, adr_id_com INT DEFAULT NULL, del_id_com INT DEFAULT NULL, create_at_com DATETIME NOT NULL, completed TINYINT(1) NOT NULL, INDEX IDX_4177D348AC18573C (adr_id_com), INDEX IDX_4177D348347BAB22 (del_id_com), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE PieceCommand (id INT AUTO_INCREMENT NOT NULL, com_id_piece_com INT DEFAULT NULL, prod_id_piece_com INT DEFAULT NULL, nb_prod INT NOT NULL, INDEX IDX_7A07E462D3121940 (com_id_piece_com), INDEX IDX_7A07E46274EB3D94 (prod_id_piece_com), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Adress (id INT AUTO_INCREMENT NOT NULL, street VARCHAR(255) NOT NULL, zip_code INT NOT NULL, city VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CompanyDelivery (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, area LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Delivery (id INT AUTO_INCREMENT NOT NULL, comp_del_id_del INT NOT NULL, date_del DATE DEFAULT NULL, price_del INT NOT NULL, INDEX IDX_CEF78E46875C0859 (comp_del_id_del), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Product (id INT AUTO_INCREMENT NOT NULL, name_prod VARCHAR(255) NOT NULL, img_prod VARCHAR(255) DEFAULT NULL, desc_prod LONGTEXT DEFAULT NULL, stock_prod INT NOT NULL, code_prod VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1CF73D31835F7B56 (code_prod), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Category (id INT AUTO_INCREMENT NOT NULL, name_cat VARCHAR(255) NOT NULL, img_cat VARCHAR(255) DEFAULT NULL, code_cat VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_FF3A7B97767E7402 (code_cat), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CategoriesProducts (prod_id_cat INT NOT NULL, cat_id_prod INT NOT NULL, INDEX IDX_4BC8B0E1D4C7C581 (prod_id_cat), INDEX IDX_4BC8B0E11E258FC3 (cat_id_prod), PRIMARY KEY(prod_id_cat, cat_id_prod)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VariantProduct (id INT AUTO_INCREMENT NOT NULL, prod_id_prod_var INT DEFAULT NULL, name_var_prod VARCHAR(255) NOT NULL, img_var_prod VARCHAR(255) DEFAULT NULL, desc_var_prod LONGTEXT DEFAULT NULL, stock_var_prod INT NOT NULL, code_var_prod VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3DFA05BCB304425F (code_var_prod), INDEX IDX_3DFA05BCDADD227B (prod_id_prod_var), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE User ADD CONSTRAINT FK_2DA17977EB5D4312 FOREIGN KEY (adr_id_user) REFERENCES Adress (id)');
        $this->addSql('ALTER TABLE Comment ADD CONSTRAINT FK_5BC96BF0ED8E3DF0 FOREIGN KEY (user_id_comment) REFERENCES User (id)');
        $this->addSql('ALTER TABLE Comment ADD CONSTRAINT FK_5BC96BF09A7C4C37 FOREIGN KEY (prod_id_comment) REFERENCES VariantProduct (id)');
        $this->addSql('ALTER TABLE Command ADD CONSTRAINT FK_4177D348AC18573C FOREIGN KEY (adr_id_com) REFERENCES Adress (id)');
        $this->addSql('ALTER TABLE Command ADD CONSTRAINT FK_4177D348347BAB22 FOREIGN KEY (del_id_com) REFERENCES Delivery (id)');
        $this->addSql('ALTER TABLE PieceCommand ADD CONSTRAINT FK_7A07E462D3121940 FOREIGN KEY (com_id_piece_com) REFERENCES Command (id)');
        $this->addSql('ALTER TABLE PieceCommand ADD CONSTRAINT FK_7A07E46274EB3D94 FOREIGN KEY (prod_id_piece_com) REFERENCES VariantProduct (id)');
        $this->addSql('ALTER TABLE Delivery ADD CONSTRAINT FK_CEF78E46875C0859 FOREIGN KEY (comp_del_id_del) REFERENCES CompanyDelivery (id)');
        $this->addSql('ALTER TABLE CategoriesProducts ADD CONSTRAINT FK_4BC8B0E1D4C7C581 FOREIGN KEY (prod_id_cat) REFERENCES Category (id)');
        $this->addSql('ALTER TABLE CategoriesProducts ADD CONSTRAINT FK_4BC8B0E11E258FC3 FOREIGN KEY (cat_id_prod) REFERENCES Product (id)');
        $this->addSql('ALTER TABLE VariantProduct ADD CONSTRAINT FK_3DFA05BCDADD227B FOREIGN KEY (prod_id_prod_var) REFERENCES Product (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Comment DROP FOREIGN KEY FK_5BC96BF0ED8E3DF0');
        $this->addSql('ALTER TABLE PieceCommand DROP FOREIGN KEY FK_7A07E462D3121940');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA17977EB5D4312');
        $this->addSql('ALTER TABLE Command DROP FOREIGN KEY FK_4177D348AC18573C');
        $this->addSql('ALTER TABLE Delivery DROP FOREIGN KEY FK_CEF78E46875C0859');
        $this->addSql('ALTER TABLE Command DROP FOREIGN KEY FK_4177D348347BAB22');
        $this->addSql('ALTER TABLE CategoriesProducts DROP FOREIGN KEY FK_4BC8B0E11E258FC3');
        $this->addSql('ALTER TABLE VariantProduct DROP FOREIGN KEY FK_3DFA05BCDADD227B');
        $this->addSql('ALTER TABLE CategoriesProducts DROP FOREIGN KEY FK_4BC8B0E1D4C7C581');
        $this->addSql('ALTER TABLE Comment DROP FOREIGN KEY FK_5BC96BF09A7C4C37');
        $this->addSql('ALTER TABLE PieceCommand DROP FOREIGN KEY FK_7A07E46274EB3D94');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE Comment');
        $this->addSql('DROP TABLE Command');
        $this->addSql('DROP TABLE PieceCommand');
        $this->addSql('DROP TABLE Adress');
        $this->addSql('DROP TABLE CompanyDelivery');
        $this->addSql('DROP TABLE Delivery');
        $this->addSql('DROP TABLE Product');
        $this->addSql('DROP TABLE Category');
        $this->addSql('DROP TABLE CategoriesProducts');
        $this->addSql('DROP TABLE VariantProduct');
    }
}
