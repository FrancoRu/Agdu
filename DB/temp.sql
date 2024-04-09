


SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';


CREATE TABLE Account (
  account_id CHAR NOT NULL,
  description VARCHAR(100) NOT NULL,
  PRIMARY KEY (account_id))
ENGINE = InnoDB;



CREATE TABLE Education (
  education_id CHAR NOT NULL ,
  description VARCHAR(100) NOT NULL,
  PRIMARY KEY (education_id))
ENGINE = InnoDB;


CREATE TABLE Beneficiary (
  user_id VARCHAR(11) NOT NULL,
  type_account CHAR NULL,
  name_beneficiary VARCHAR(255) NULL,
  lastname_beneficiary VARCHAR(255) NULL,
  CBU_beneficiary VARCHAR(22) NULL,
  email_beneficiary VARCHAR(255) NULL,
  PRIMARY KEY (user_id),
  CONSTRAINT fk_Beneficiario_Account
    FOREIGN KEY (type_account)
    REFERENCES  Account (account_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;



CREATE TABLE Children (
  children_id VARCHAR(255) NOT NULL,
  user_id VARCHAR(11) NOT NULL,
  education_level CHAR NOT NULL,
  name_children VARCHAR(255) NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  url_file VARCHAR(255) NOT NULL,
  PRIMARY KEY (children_id),
  CONSTRAINT fk_Children_Beneficiario1
    FOREIGN KEY (user_id)
    REFERENCES Beneficiario (user_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_Children_Education1
    FOREIGN KEY (education_level)
    REFERENCES Education (education_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

