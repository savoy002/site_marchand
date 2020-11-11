<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201110091520 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, adr_id_user INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles TEXT NOT NULL COMMENT \'(DC2Type:json_array)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at_user DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, valid_user TINYINT(1) DEFAULT \'0\' NOT NULL, deleted_user TINYINT(1) DEFAULT \'0\' NOT NULL, admin_user TINYINT(1) DEFAULT \'0\' NOT NULL, super_admin TINYINT(1) DEFAULT \'0\' NOT NULL, img VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_2DA17977F85E0677 (username), UNIQUE INDEX UNIQ_2DA17977BBC2C8AC (img), INDEX IDX_2DA17977EB5D4312 (adr_id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Comment (id INT AUTO_INCREMENT NOT NULL, user_id_comment INT DEFAULT NULL, prod_id_comment INT DEFAULT NULL, mark_comment INT NOT NULL, text_comment LONGTEXT NOT NULL, created_at_comment DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, deleted_comment TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_5BC96BF0ED8E3DF0 (user_id_comment), INDEX IDX_5BC96BF09A7C4C37 (prod_id_comment), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Access (id INT AUTO_INCREMENT NOT NULL, id_user_acc INT NOT NULL, code_acc VARCHAR(255) NOT NULL, created_at_acc DATETIME NOT NULL, used_acc TINYINT(1) NOT NULL, INDEX IDX_1C52E6297F2FD9D (id_user_acc), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Command (id INT AUTO_INCREMENT NOT NULL, adr_id_com INT DEFAULT NULL, del_id_com INT DEFAULT NULL, user_id_com INT DEFAULT NULL, type_del_id_com INT DEFAULT NULL, create_at_com DATETIME NOT NULL, completed TINYINT(1) NOT NULL, date_receive DATETIME DEFAULT NULL, is_basket TINYINT(1) NOT NULL, price_total INT DEFAULT NULL, deleted_com TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_4177D348AC18573C (adr_id_com), INDEX IDX_4177D348347BAB22 (del_id_com), INDEX IDX_4177D3489F403581 (user_id_com), INDEX IDX_4177D34829787D07 (type_del_id_com), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE TypeDelivery (id INT AUTO_INCREMENT NOT NULL, comp_del_id_del INT NOT NULL, name_type_del VARCHAR(255) NOT NULL, price_type_del INT NOT NULL, time_min_type_del INT NOT NULL, time_max_type_del INT NOT NULL, activate_type_del TINYINT(1) DEFAULT \'0\' NOT NULL, deleted_type_del TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_8AAFDB36875C0859 (comp_del_id_del), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE PieceCommand (id INT AUTO_INCREMENT NOT NULL, com_id_piece_com INT DEFAULT NULL, prod_id_piece_com INT DEFAULT NULL, nb_prod INT NOT NULL, INDEX IDX_7A07E462D3121940 (com_id_piece_com), INDEX IDX_7A07E46274EB3D94 (prod_id_piece_com), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Adress (id INT AUTO_INCREMENT NOT NULL, street VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, deleted_adr TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CompanyDelivery (id INT AUTO_INCREMENT NOT NULL, name_comp_del VARCHAR(255) NOT NULL, area TEXT NOT NULL COMMENT \'(DC2Type:json_array)\', logo VARCHAR(255) DEFAULT NULL, activate_comp_del TINYINT(1) DEFAULT \'0\' NOT NULL, deleted_comp_del TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_12284B72E48E9A13 (logo), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Delivery (id INT AUTO_INCREMENT NOT NULL, type_del_id_del INT NOT NULL, date_del DATE DEFAULT NULL, deleted_del TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_CEF78E46A1DFB39E (type_del_id_del), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Product (id INT AUTO_INCREMENT NOT NULL, name_prod VARCHAR(255) NOT NULL, img_prod VARCHAR(255) DEFAULT NULL, desc_prod LONGTEXT DEFAULT NULL, stock_prod INT NOT NULL, code_prod VARCHAR(255) NOT NULL, activate_prod TINYINT(1) DEFAULT \'0\' NOT NULL, deleted_prod TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_1CF73D31835F7B56 (code_prod), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Category (id INT AUTO_INCREMENT NOT NULL, name_cat VARCHAR(255) NOT NULL, img_cat VARCHAR(255) DEFAULT NULL, code_cat VARCHAR(255) NOT NULL, activate_cat TINYINT(1) DEFAULT \'0\' NOT NULL, deleted_cat TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_FF3A7B97767E7402 (code_cat), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CategoriesProducts (prod_id_cat INT NOT NULL, cat_id_prod INT NOT NULL, INDEX IDX_4BC8B0E1D4C7C581 (prod_id_cat), INDEX IDX_4BC8B0E11E258FC3 (cat_id_prod), PRIMARY KEY(prod_id_cat, cat_id_prod)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CategoriesVariantsProducts (prod_var_id_cat INT NOT NULL, cat_id_prod_var INT NOT NULL, INDEX IDX_A33B8CCD980BA87 (prod_var_id_cat), INDEX IDX_A33B8CCDBCD66E2D (cat_id_prod_var), PRIMARY KEY(prod_var_id_cat, cat_id_prod_var)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VariantProduct (id INT AUTO_INCREMENT NOT NULL, prod_id_var_prod INT DEFAULT NULL, name_var_prod VARCHAR(255) NOT NULL, img_var_prod VARCHAR(255) DEFAULT NULL, desc_var_prod LONGTEXT DEFAULT NULL, stock_var_prod INT NOT NULL, code_var_prod VARCHAR(255) NOT NULL, price INT NOT NULL, created_at_var_prod DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, is_wellcome_var_prod TINYINT(1) DEFAULT \'0\' NOT NULL, activate_var_prod TINYINT(1) DEFAULT \'0\' NOT NULL, deleted_var_prod TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_3DFA05BCB304425F (code_var_prod), INDEX IDX_3DFA05BCB632BA16 (prod_id_var_prod), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE User ADD CONSTRAINT FK_2DA17977EB5D4312 FOREIGN KEY (adr_id_user) REFERENCES Adress (id)');
        $this->addSql('ALTER TABLE Comment ADD CONSTRAINT FK_5BC96BF0ED8E3DF0 FOREIGN KEY (user_id_comment) REFERENCES User (id)');
        $this->addSql('ALTER TABLE Comment ADD CONSTRAINT FK_5BC96BF09A7C4C37 FOREIGN KEY (prod_id_comment) REFERENCES VariantProduct (id)');
        $this->addSql('ALTER TABLE Access ADD CONSTRAINT FK_1C52E6297F2FD9D FOREIGN KEY (id_user_acc) REFERENCES User (id)');
        $this->addSql('ALTER TABLE Command ADD CONSTRAINT FK_4177D348AC18573C FOREIGN KEY (adr_id_com) REFERENCES Adress (id)');
        $this->addSql('ALTER TABLE Command ADD CONSTRAINT FK_4177D348347BAB22 FOREIGN KEY (del_id_com) REFERENCES Delivery (id)');
        $this->addSql('ALTER TABLE Command ADD CONSTRAINT FK_4177D3489F403581 FOREIGN KEY (user_id_com) REFERENCES User (id)');
        $this->addSql('ALTER TABLE Command ADD CONSTRAINT FK_4177D34829787D07 FOREIGN KEY (type_del_id_com) REFERENCES TypeDelivery (id)');
        $this->addSql('ALTER TABLE TypeDelivery ADD CONSTRAINT FK_8AAFDB36875C0859 FOREIGN KEY (comp_del_id_del) REFERENCES CompanyDelivery (id)');
        $this->addSql('ALTER TABLE PieceCommand ADD CONSTRAINT FK_7A07E462D3121940 FOREIGN KEY (com_id_piece_com) REFERENCES Command (id)');
        $this->addSql('ALTER TABLE PieceCommand ADD CONSTRAINT FK_7A07E46274EB3D94 FOREIGN KEY (prod_id_piece_com) REFERENCES VariantProduct (id)');
        $this->addSql('ALTER TABLE Delivery ADD CONSTRAINT FK_CEF78E46A1DFB39E FOREIGN KEY (type_del_id_del) REFERENCES TypeDelivery (id)');
        $this->addSql('ALTER TABLE CategoriesProducts ADD CONSTRAINT FK_4BC8B0E1D4C7C581 FOREIGN KEY (prod_id_cat) REFERENCES Category (id)');
        $this->addSql('ALTER TABLE CategoriesProducts ADD CONSTRAINT FK_4BC8B0E11E258FC3 FOREIGN KEY (cat_id_prod) REFERENCES Product (id)');
        $this->addSql('ALTER TABLE CategoriesVariantsProducts ADD CONSTRAINT FK_A33B8CCD980BA87 FOREIGN KEY (prod_var_id_cat) REFERENCES Category (id)');
        $this->addSql('ALTER TABLE CategoriesVariantsProducts ADD CONSTRAINT FK_A33B8CCDBCD66E2D FOREIGN KEY (cat_id_prod_var) REFERENCES VariantProduct (id)');
        $this->addSql('ALTER TABLE VariantProduct ADD CONSTRAINT FK_3DFA05BCB632BA16 FOREIGN KEY (prod_id_var_prod) REFERENCES Product (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Comment DROP FOREIGN KEY FK_5BC96BF0ED8E3DF0');
        $this->addSql('ALTER TABLE Access DROP FOREIGN KEY FK_1C52E6297F2FD9D');
        $this->addSql('ALTER TABLE Command DROP FOREIGN KEY FK_4177D3489F403581');
        $this->addSql('ALTER TABLE PieceCommand DROP FOREIGN KEY FK_7A07E462D3121940');
        $this->addSql('ALTER TABLE Command DROP FOREIGN KEY FK_4177D34829787D07');
        $this->addSql('ALTER TABLE Delivery DROP FOREIGN KEY FK_CEF78E46A1DFB39E');
        $this->addSql('ALTER TABLE User DROP FOREIGN KEY FK_2DA17977EB5D4312');
        $this->addSql('ALTER TABLE Command DROP FOREIGN KEY FK_4177D348AC18573C');
        $this->addSql('ALTER TABLE TypeDelivery DROP FOREIGN KEY FK_8AAFDB36875C0859');
        $this->addSql('ALTER TABLE Command DROP FOREIGN KEY FK_4177D348347BAB22');
        $this->addSql('ALTER TABLE CategoriesProducts DROP FOREIGN KEY FK_4BC8B0E11E258FC3');
        $this->addSql('ALTER TABLE VariantProduct DROP FOREIGN KEY FK_3DFA05BCB632BA16');
        $this->addSql('ALTER TABLE CategoriesProducts DROP FOREIGN KEY FK_4BC8B0E1D4C7C581');
        $this->addSql('ALTER TABLE CategoriesVariantsProducts DROP FOREIGN KEY FK_A33B8CCD980BA87');
        $this->addSql('ALTER TABLE Comment DROP FOREIGN KEY FK_5BC96BF09A7C4C37');
        $this->addSql('ALTER TABLE PieceCommand DROP FOREIGN KEY FK_7A07E46274EB3D94');
        $this->addSql('ALTER TABLE CategoriesVariantsProducts DROP FOREIGN KEY FK_A33B8CCDBCD66E2D');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE Comment');
        $this->addSql('DROP TABLE Access');
        $this->addSql('DROP TABLE Command');
        $this->addSql('DROP TABLE TypeDelivery');
        $this->addSql('DROP TABLE PieceCommand');
        $this->addSql('DROP TABLE Adress');
        $this->addSql('DROP TABLE CompanyDelivery');
        $this->addSql('DROP TABLE Delivery');
        $this->addSql('DROP TABLE Product');
        $this->addSql('DROP TABLE Category');
        $this->addSql('DROP TABLE CategoriesProducts');
        $this->addSql('DROP TABLE CategoriesVariantsProducts');
        $this->addSql('DROP TABLE VariantProduct');
    }
}
