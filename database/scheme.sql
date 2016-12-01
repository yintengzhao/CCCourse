-- MySQL Script generated by MySQL Workbench
-- 2016年12月01日 星期四 10时22分36秒
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema CCCourse
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `CCCourse` ;

-- -----------------------------------------------------
-- Schema CCCourse
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `CCCourse` DEFAULT CHARACTER SET utf8 ;
USE `CCCourse` ;

-- -----------------------------------------------------
-- Table `CCCourse`.`amdin`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `CCCourse`.`amdin` ;

CREATE TABLE IF NOT EXISTS `CCCourse`.`amdin` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `account` CHAR(32) NOT NULL,
  `name` CHAR(8) NOT NULL,
  `privilege` INT NULL DEFAULT 1,
  `time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `lastlogin` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `account_UNIQUE` (`account` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `CCCourse`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `CCCourse`.`user` ;

CREATE TABLE IF NOT EXISTS `CCCourse`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `number` CHAR(12) NOT NULL,
  `name` CHAR(8) NOT NULL,
  `status` INT UNSIGNED NULL DEFAULT 1,
  `type` CHAR(8) NULL,
  `time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `lastlogin` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `index2` (`number` ASC, `type` ASC),
  INDEX `index3` (`number` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `CCCourse`.`course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `CCCourse`.`course` ;

CREATE TABLE IF NOT EXISTS `CCCourse`.`course` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `number` CHAR(16) NOT NULL COMMENT '课程编号\n',
  `name` CHAR(32) NOT NULL,
  `code` CHAR(8) NULL DEFAULT '92036000' COMMENT '院系代码',
  `college` CHAR(50) NULL DEFAULT '计算机与控制工程学院',
  `name_en` CHAR(100) NULL,
  `description` TEXT NULL,
  `hours` INT NULL DEFAULT 32,
  `reference` TEXT NULL,
  `book` TEXT NULL,
  `exem` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `number_UNIQUE` (`number` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `CCCourse`.`evaluation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `CCCourse`.`evaluation` ;

CREATE TABLE IF NOT EXISTS `CCCourse`.`evaluation` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `course_id` INT NOT NULL,
  `admin_id` INT NULL,
  `user_id` INT NOT NULL,
  `status` INT NULL DEFAULT 1,
  `time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `addcourse` TEXT NULL,
  `advice` TEXT NULL,
  `score1` INT NULL,
  `score2` INT NULL,
  `score3` INT NULL,
  `score4` INT NULL,
  `score5` INT NULL,
  `score6` INT NULL,
  `score7` INT NULL,
  `score8` INT NULL,
  `score9` INT NULL,
  `score10` INT NULL,
  `score11` INT NULL,
  `score12` INT NULL,
  `averge` FLOAT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_student_evaluation_user1_idx` (`user_id` ASC),
  INDEX `fk_student_evaluation_course1_idx` (`course_id` ASC),
  INDEX `fk_student_evaluation_amdin1_idx` (`admin_id` ASC),
  CONSTRAINT `fk_student_evaluation_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `CCCourse`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_evaluation_course1`
    FOREIGN KEY (`course_id`)
    REFERENCES `CCCourse`.`course` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_evaluation_amdin1`
    FOREIGN KEY (`admin_id`)
    REFERENCES `CCCourse`.`amdin` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `CCCourse`.`advice`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `CCCourse`.`advice` ;

CREATE TABLE IF NOT EXISTS `CCCourse`.`advice` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `admin_id` INT NULL,
  `type` INT NOT NULL COMMENT '0-2计算机博士，学硕，专硕 3-5控制博士，学硕，专硕',
  `user_id` INT NOT NULL,
  `status` INT NULL DEFAULT 1,
  `time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `addcourse` TEXT NULL,
  `advice` TEXT NULL,
  `score1` INT NULL,
  `score2` INT NULL,
  `score3` INT NULL,
  `score4` INT NULL,
  `score5` INT NULL,
  `score6` INT NULL,
  `score7` INT NULL,
  `score8` INT NULL,
  `score9` INT NULL,
  `averge` FLOAT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_teacher_evaluation_amdin1_idx` (`admin_id` ASC),
  CONSTRAINT `fk_teacher_evaluation_amdin1`
    FOREIGN KEY (`admin_id`)
    REFERENCES `CCCourse`.`amdin` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `CCCourse`.`evaluationvote`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `CCCourse`.`evaluationvote` ;

CREATE TABLE IF NOT EXISTS `CCCourse`.`evaluationvote` (
  `evaluation_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `vote` INT NOT NULL,
  `time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`evaluation_id`, `user_id`),
  INDEX `fk_stu_eval_vote_2_idx` (`user_id` ASC),
  CONSTRAINT `fk_stu_eval_vote_1`
    FOREIGN KEY (`evaluation_id`)
    REFERENCES `CCCourse`.`evaluation` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_stu_eval_vote_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `CCCourse`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `CCCourse`.`advicevote`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `CCCourse`.`advicevote` ;

CREATE TABLE IF NOT EXISTS `CCCourse`.`advicevote` (
  `advice_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `vote` INT NOT NULL,
  `time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `index1` (`advice_id` ASC),
  CONSTRAINT `fk_teac_eval_vote_1`
    FOREIGN KEY (`advice_id`)
    REFERENCES `CCCourse`.`advice` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
