-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema bapd9699_app
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema bapd9699_app
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `bapd9699_app` DEFAULT CHARACTER SET utf8 ;
USE `bapd9699_app` ;

-- -----------------------------------------------------
-- Table `bapd9699_app`.`ROLE`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bapd9699_app`.`ROLE` ;

CREATE TABLE IF NOT EXISTS `bapd9699_app`.`ROLE` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `ROLE_NAME` VARCHAR(255) NOT NULL,
  `STATUS` VARCHAR(1) NOT NULL DEFAULT '1',
  `CREATE_DATE` DATETIME NOT NULL DEFAULT NOW(),
  `UPDATE_DATE` DATETIME NOT NULL DEFAULT NOW() ON UPDATE NOW(),
  PRIMARY KEY (`ID`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bapd9699_app`.`USER`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bapd9699_app`.`USER` ;

CREATE TABLE IF NOT EXISTS `bapd9699_app`.`USER` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `USERNAME` VARCHAR(255) NOT NULL,
  `PASSWORD` VARCHAR(255) NOT NULL,
  `STATUS` VARCHAR(1) NOT NULL DEFAULT '1',
  `CREATE_DATE` DATETIME NOT NULL DEFAULT NOW(),
  `UPDATE_DATE` DATETIME NOT NULL DEFAULT NOW() ON UPDATE NOW(),
  `ROLE_ID` INT NOT NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_USER_ROLE_idx` (`ROLE_ID` ASC),
  UNIQUE INDEX `USERNAME_UNIQUE` (`USERNAME` ASC),
  CONSTRAINT `fk_USER_ROLE`
    FOREIGN KEY (`ROLE_ID`)
    REFERENCES `bapd9699_app`.`ROLE` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bapd9699_app`.`MENU`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bapd9699_app`.`MENU` ;

CREATE TABLE IF NOT EXISTS `bapd9699_app`.`MENU` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `MENU_NAME` VARCHAR(255) NOT NULL,
  `PERMALINK` TEXT NOT NULL,
  `MENU_ICON` VARCHAR(255) NOT NULL,
  `MENU_ORDER` VARCHAR(10) NOT NULL,
  `STATUS` VARCHAR(1) NOT NULL DEFAULT '1',
  `CREATE_DATE` DATETIME NOT NULL DEFAULT NOW(),
  `UPDATE_DATE` DATETIME NOT NULL DEFAULT NOW() ON UPDATE NOW(),
  `MENU_ID` INT NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_MENU_MENU1_idx` (`MENU_ID` ASC),
  CONSTRAINT `fk_MENU_MENU1`
    FOREIGN KEY (`MENU_ID`)
    REFERENCES `bapd9699_app`.`MENU` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bapd9699_app`.`ROLE_MENU`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bapd9699_app`.`ROLE_MENU` ;

CREATE TABLE IF NOT EXISTS `bapd9699_app`.`ROLE_MENU` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `ROLE_ID` INT NOT NULL,
  `MENU_ID` INT NOT NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_ROLE_has_MENU_MENU1_idx` (`MENU_ID` ASC),
  INDEX `fk_ROLE_has_MENU_ROLE1_idx` (`ROLE_ID` ASC),
  CONSTRAINT `fk_ROLE_has_MENU_ROLE1`
    FOREIGN KEY (`ROLE_ID`)
    REFERENCES `bapd9699_app`.`ROLE` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ROLE_has_MENU_MENU1`
    FOREIGN KEY (`MENU_ID`)
    REFERENCES `bapd9699_app`.`MENU` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bapd9699_app`.`USER_INFO`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bapd9699_app`.`USER_INFO` ;

CREATE TABLE IF NOT EXISTS `bapd9699_app`.`USER_INFO` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `ALIAS` TEXT NOT NULL,
  `EMAIL` TEXT NULL,
  `PHONE` VARCHAR(255) NULL,
  `ADDRESS` TEXT NULL,
  `PHOTO_1` TEXT NULL,
  `USER_ID` INT NOT NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_USER_INFO_USER1_idx` (`USER_ID` ASC),
  CONSTRAINT `fk_USER_INFO_USER1`
    FOREIGN KEY (`USER_ID`)
    REFERENCES `bapd9699_app`.`USER` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bapd9699_app`.`APP_DATA`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bapd9699_app`.`APP_DATA` ;

CREATE TABLE IF NOT EXISTS `bapd9699_app`.`APP_DATA` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `NAME` VARCHAR(250) NULL,
  `ICON` VARCHAR(45) NULL,
  `FAVICON` TEXT NULL,
  `NOTES` TEXT NULL,
  `CREATE_DATE` DATETIME NOT NULL DEFAULT NOW(),
  `UPDATE_DATE` DATETIME NOT NULL DEFAULT NOW() ON UPDATE NOW(),
  `USER_ID` INT NOT NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_APP_DATA_USER1_idx` (`USER_ID` ASC),
  CONSTRAINT `fk_APP_DATA_USER1`
    FOREIGN KEY (`USER_ID`)
    REFERENCES `bapd9699_app`.`USER` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bapd9699_app`.`LITMAS`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bapd9699_app`.`LITMAS` ;

CREATE TABLE IF NOT EXISTS `bapd9699_app`.`LITMAS` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `NAMA_LITMAS` TEXT NOT NULL,
  `STATUS` VARCHAR(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bapd9699_app`.`JENIS_PEMBIMBINGAN`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bapd9699_app`.`JENIS_PEMBIMBINGAN` ;

CREATE TABLE IF NOT EXISTS `bapd9699_app`.`JENIS_PEMBIMBINGAN` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `NAMA_JENIS` TEXT NOT NULL,
  PRIMARY KEY (`ID`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bapd9699_app`.`PEMBIMBINGAN`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bapd9699_app`.`PEMBIMBINGAN` ;

CREATE TABLE IF NOT EXISTS `bapd9699_app`.`PEMBIMBINGAN` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `NO_REGISTER` TEXT NOT NULL,
  `TGL_REGISTER` DATE NOT NULL,
  `NAMA` TEXT NOT NULL,
  `KASUS` TEXT NOT NULL,
  `KET_BERKAS` VARCHAR(255) NOT NULL,
  `STATUS` VARCHAR(1) NOT NULL DEFAULT '1',
  `CREATE_DATE` DATETIME NOT NULL DEFAULT NOW(),
  `UPDATE_DATE` DATETIME NOT NULL DEFAULT NOW() ON UPDATE NOW(),
  `LITMAS_ID` INT NOT NULL,
  `JENIS_PEMBIMBINGAN_ID` INT NOT NULL,
  `USER_ID` INT NOT NULL,
  PRIMARY KEY (`ID`),
  INDEX `fk_PEMBIMBINGAN_LITMAS1_idx` (`LITMAS_ID` ASC),
  INDEX `fk_PEMBIMBINGAN_JENIS_PEMBIMBINGAN1_idx` (`JENIS_PEMBIMBINGAN_ID` ASC),
  INDEX `fk_PEMBIMBINGAN_USER1_idx` (`USER_ID` ASC),
  CONSTRAINT `fk_PEMBIMBINGAN_LITMAS1`
    FOREIGN KEY (`LITMAS_ID`)
    REFERENCES `bapd9699_app`.`LITMAS` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_PEMBIMBINGAN_JENIS_PEMBIMBINGAN1`
    FOREIGN KEY (`JENIS_PEMBIMBINGAN_ID`)
    REFERENCES `bapd9699_app`.`JENIS_PEMBIMBINGAN` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_PEMBIMBINGAN_USER1`
    FOREIGN KEY (`USER_ID`)
    REFERENCES `bapd9699_app`.`USER` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bapd9699_app`.`PENGAJUAN_LITMAS`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bapd9699_app`.`PENGAJUAN_LITMAS` ;

CREATE TABLE IF NOT EXISTS `bapd9699_app`.`PENGAJUAN_LITMAS` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `DARI` TEXT NOT NULL,
  `PERIHAL` TEXT NOT NULL,
  `TELEPON` VARCHAR(45) NOT NULL,
  `FILE` TEXT NOT NULL,
  `STATUS` VARCHAR(1) NOT NULL DEFAULT '1',
  `CREATE_DATE` DATETIME NOT NULL DEFAULT NOW(),
  `UPDATE_DATE` DATETIME NOT NULL DEFAULT NOW() ON UPDATE NOW(),
  PRIMARY KEY (`ID`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `bapd9699_app`.`ROLE`
-- -----------------------------------------------------
START TRANSACTION;
USE `bapd9699_app`;
INSERT INTO `bapd9699_app`.`ROLE` (`ID`, `ROLE_NAME`, `STATUS`, `CREATE_DATE`, `UPDATE_DATE`) VALUES (DEFAULT, 'administrator', DEFAULT, DEFAULT, DEFAULT);

COMMIT;


-- -----------------------------------------------------
-- Data for table `bapd9699_app`.`USER`
-- -----------------------------------------------------
START TRANSACTION;
USE `bapd9699_app`;
INSERT INTO `bapd9699_app`.`USER` (`ID`, `USERNAME`, `PASSWORD`, `STATUS`, `CREATE_DATE`, `UPDATE_DATE`, `ROLE_ID`) VALUES (DEFAULT, 'prsty', 'c61a56c2b825813586744dfde2f2aad1', DEFAULT, DEFAULT, DEFAULT, 1);

COMMIT;

