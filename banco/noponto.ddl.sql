-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema noponto
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema noponto
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `noponto` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `noponto` ;

-- -----------------------------------------------------
-- Table `noponto`.`empresas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `noponto`.`empresas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_original` INT NOT NULL,
  `nome` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_original_UNIQUE` (`id_original` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `noponto`.`rotas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `noponto`.`rotas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_original` INT NOT NULL,
  `nome_original` VARCHAR(100) NOT NULL,
  `sentido` VARCHAR(45) NOT NULL COMMENT 'Ida ou volta.',
  `numero` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_original_UNIQUE` (`id_original` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `noponto`.`linhas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `noponto`.`linhas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `numero` VARCHAR(45) NOT NULL,
  `nome` VARCHAR(45) NOT NULL DEFAULT '[NOME]',
  `empresa_id` INT NOT NULL,
  `rota_ida_id` INT NOT NULL,
  `rota_volta_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nome_original_UNIQUE` (`nome` ASC),
  UNIQUE INDEX `empresa_id_UNIQUE` (`empresa_id` ASC),
  INDEX `fk_linhas_rotas1_idx` (`rota_ida_id` ASC),
  INDEX `fk_linhas_rotas2_idx` (`rota_volta_id` ASC),
  CONSTRAINT `fk_linhas_empresas`
    FOREIGN KEY (`empresa_id`)
    REFERENCES `noponto`.`empresas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_linhas_rota_ida`
    FOREIGN KEY (`rota_ida_id`)
    REFERENCES `noponto`.`rotas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_linhas_rota_volta`
    FOREIGN KEY (`rota_volta_id`)
    REFERENCES `noponto`.`rotas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `noponto`.`ponto_tipos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `noponto`.`ponto_tipos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_original` INT NOT NULL,
  `nome` VARCHAR(45) NOT NULL COMMENT 'Tipos de ponto (ponto comum, terminal, etc.).',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `noponto`.`pontos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `noponto`.`pontos` (
  `id` INT NULL AUTO_INCREMENT,
  `id_original` INT NOT NULL,
  `nome` VARCHAR(45) NULL DEFAULT 'Ponto',
  `gps` POINT NOT NULL,
  `sentido` INT NOT NULL DEFAULT 0 COMMENT 'Em graus.',
  `tipo` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `gps_UNIQUE` (`gps` ASC),
  INDEX `fk_pontos_ponto_tipos_idx` (`tipo` ASC),
  UNIQUE INDEX `id_original_UNIQUE` (`id_original` ASC),
  CONSTRAINT `fk_tipo`
    FOREIGN KEY (`tipo`)
    REFERENCES `noponto`.`ponto_tipos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `noponto`.`traducoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `noponto`.`traducoes` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Traduções da API original para o projeto.',
  `termo_original` VARCHAR(45) NOT NULL,
  `termo_traduzido` VARCHAR(45) NOT NULL,
  `descricao` MEDIUMTEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `termo_original_UNIQUE` (`termo_original` ASC),
  UNIQUE INDEX `termo_traduzido_UNIQUE` (`termo_traduzido` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `noponto`.`rota_contem_pontos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `noponto`.`rota_contem_pontos` (
  `rota_id` INT NOT NULL,
  `ponto_id` INT NOT NULL COMMENT 'Uma rota contém vários pontos. Um ponto pertence a várias rotas.',
  `ordem_ponto` INT NOT NULL DEFAULT 1 COMMENT 'Ordenação do ponto dentro da rota.',
  PRIMARY KEY (`rota_id`, `ponto_id`, `ordem_ponto`),
  INDEX `fk_ponto_idx` (`ponto_id` ASC),
  INDEX `fk_rota_idx` (`rota_id` ASC),
  CONSTRAINT `fk_rota`
    FOREIGN KEY (`rota_id`)
    REFERENCES `noponto`.`rotas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ponto`
    FOREIGN KEY (`ponto_id`)
    REFERENCES `noponto`.`pontos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
