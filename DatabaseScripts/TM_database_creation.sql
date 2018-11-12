/*Creates TubeMaster database following the specifications laid out in our design documentation
  Team JayZ
  11/5/2018
*/
CREATE EXTENSION pgcrypto;

DROP TABLE IF EXISTS bdd_racks;
DROP TABLE IF EXISTS ttd_racks;
DROP TABLE IF EXISTS so_sets;
DROP TABLE IF EXISTS pss;
DROP TABLE IF EXISTS cal_racks;
DROP TABLE IF EXISTS cal_or_sets;
DROP TABLE IF EXISTS equipment;

DROP TABLE IF EXISTS units;
DROP TABLE IF EXISTS plants;
DROP TABLE IF EXISTS clients;

DROP TABLE IF EXISTS TMpersonnels CASCADE;
DROP TABLE IF EXISTS Projects CASCADE; 
DROP TABLE IF EXISTS projects_personnels CASCADE; 


/*Creates table Equipment, holding onto info of all of TMs inventory*/
CREATE TABLE equipment (
       PRIMARY KEY(equipment_id),
       equipment_id                      SERIAL,
       equipment_name                    VARCHAR(256)    NOT NULL,
       equipment_sn                      INT             DEFAULT NULL,
       equipment_quantity                INT             NOT NULL
                                         CONSTRAINT negative_quantity -- Quantity cannot be less than 0
                                         CHECK (equipment_quantity >= 0),
       equipment_notes                   VARCHAR(200)    DEFAULT NULL,
       equipment_tag                     VARCHAR(5)      NOT NULL
                                         CONSTRAINT valid_tag -- Keep tag values in valid range (see equipment_tag spec)
                                         CHECK (equipment_tag IN
                                               ('blue','Blue','red','Red',
                                                'green','Green', 'N/A')),
       equipment_location                VARCHAR(100)    NOT NULL,
       equipment_shelf_location          VARCHAR(100)    DEFAULT NULL,
       equipment_updates                 VARCHAR(256)    DEFAULT NULL,
       equipment_inventory_update_date   DATE            DEFAULT NULL,
       equipment_description             VARCHAR(500)    NOT NULL,
       equipment_modifications           VARCHAR(256)    DEFAULT NULL,
       equipment_in_out_of_service       INT             NOT NULL
                                         CONSTRAINT boolean_values -- INT with bool values 1/0 (see equipment_in_out_of_service spec)
                                         CHECK (equipment_in_out_of_service
                                                IN (1, 0)),
       equipment_potential_projects      VARCHAR(1000)   DEFAULT NULL,
       equipment_tubemaster_value        NUMERIC(10, 2)  NOT NULL
                                         CONSTRAINT negative_tm_value -- Value cannot be less than 0
                                         CHECK(equipment_tubemaster_value >= 0),
       equipment_shipping_value          NUMERIC(10, 2)  NOT NULL
                                         CONSTRAINT negative_ship_value -- Value cannot be less than 0
                                         CHECK(equipment_shipping_value >= 0),
       equipment_client_value            NUMERIC(10, 2)  DEFAULT NULL
                                         CONSTRAINT negative_client_value -- Value cannot be less than 0
                                         CHECK(equipment_client_value >= 0 OR
                                               equipment_client_value = NULL),
       equipment_weight                  INT             NOT NULL
                                         CONSTRAINT negative_weight
                                         CHECK(equipment_weight >= 0),
       equipment_cost                    NUMERIC(10, 2)  DEFAULT NULL
                                         CONSTRAINT negative_cost -- Cost cannot be less than 0
                                         CHECK(equipment_cost >= 0 OR
                                               equipment_cost = NULL),
       equipment_vendor                  VARCHAR(100)    DEFAULT NULL,
       equipment_manufacturer            VARCHAR(100)    NOT NULL,
       equipment_date_of_return          DATE            DEFAULT NULL,
       equipment_ideal_storage_location  VARCHAR(100)    DEFAULT NULL
);


/*Insert sample data into equipment*/
INSERT INTO equipment (equipment_name, equipment_quantity, equipment_tag, equipment_location,
                       equipment_description, equipment_in_out_of_service,
                       equipment_tubemaster_value, equipment_shipping_value, equipment_weight,
                       equipment_manufacturer)
VALUES ('Test Equipment', 1, 'Blue', 'Warehouse', 'This is a test equipment', 1, 2000.0,
        2000.0, 75, 'Us'),
       ('BDD Rack', 1, 'Red', 'Saudi', 'This is a BDD rack', 0, 1500.00, 2500.00, 20, 'Us'),
       ('TTD Rack', 1, 'Green', 'Illinois', 'This is a TTD Rack', 1, 1526.65, 2890.89, 22, 'Us'),
       ('SO Set', 1, 'Red', 'Saudi', 'This is an SO set', 1, 200.00, 100.00, 10, 'Us'),
       ('PS', 1, 'Red', 'Saudi', 'This is a PS', 1, 211.54, 185.97, 15, 'Us'),
       ('Cal Rack', 1, 'Green', 'Indiana', 'This is a Cal Rack', 0, 234.65, 167.98, 77, 'Us'),
       ('Cal Or Set', 1, 'Green', 'Indiana', 'This is a Cal Or Set', 1, 229.65, 167.67, 23, 'Us');
/*Create subset table for BDD Racks*/
CREATE TABLE bdd_racks (
       PRIMARY KEY(bdd_rack_id),
       bdd_rack_id         INT          NOT NULL
                           REFERENCES equipment (equipment_id)
                           ON DELETE CASCADE, -- If equipment is deleted, delete subset as well
       bdd_tube_rack_size  VARCHAR(20)  NOT NULL
);

/*Create rule restricting deletion from the BDD Racks table*/
CREATE RULE bdd_rack_id_restrict AS -- If a subset record is deleted, do not allow if still in equipment table
    ON DELETE TO bdd_racks
 WHERE (bdd_rack_id IN (SELECT equipment_id
                          FROM equipment))
    DO INSTEAD NOTHING;


/*Insert sample data from equipment into bdd_racks*/
INSERT INTO bdd_racks
VALUES (2, 'AAA');


/*Creates subset table for TTD Racks*/
CREATE TABLE ttd_racks (
       PRIMARY KEY(ttd_rack_id),
              ttd_rack_id         INT          NOT NULL
                                  REFERENCES equipment (equipment_id)
                                  ON DELETE CASCADE, -- If equipment is deleted, delete subset as well
              ttd_tube_rack_size  VARCHAR(20)  NOT NULL
                                                                           );

/*Creates rule restricting deletion from the TTD Racks table*/
CREATE RULE ttd_rack_id_restrict AS -- If a subset record is deleted, do not allow if still in equipment table
    ON DELETE TO ttd_racks
 WHERE (ttd_rack_id IN (SELECT equipment_id
                          FROM equipment))
    DO INSTEAD NOTHING;


/*Insert sample data from equipment into the ttd_racks table*/
INSERT INTO ttd_racks
VALUES (3, 'B');


/*Creates subset table for SO Sets*/
CREATE TABLE so_sets (
       PRIMARY KEY(so_set_id),
       so_set_id           INT           NOT NULL
		           REFERENCES equipment (equipment_id)
			   ON DELETE CASCADE, -- If equipment is deleted, delete subset as well
       so_case_number      INT           NOT NULL
                           CONSTRAINT negative_case_number -- Case number cannot be negative
			   CHECK(so_case_number >= 0),
       so_size             NUMERIC(4, 3) NOT NULL
			   CONSTRAINT negative_size -- Size cannot be negative
			   CHECK(so_size >= 0),
       so_set_label        VARCHAR(10)   NOT NULL,
       so_number_in_set    INT NOT NULL
                           CONSTRAINT negative_quantity -- Number in a set cannot be negative
			   CHECK(so_number_in_set >= 0),
       so_notes            VARCHAR(200)  DEFAULT NULL
);


/*Creates rule restricting deletion from the SO Sets table*/
CREATE RULE so_set_id_restrict AS -- If a subset record is deleted, do not allow if still in equipment table
    ON DELETE TO so_sets
 WHERE (so_set_id IN (SELECT equipment_id
                          FROM equipment))
    DO INSTEAD NOTHING;


/*Inserts sample data from equipment into so_sets*/
INSERT INTO so_sets
VALUES (4, 1, 0.040, 'AA', 12);


/*Creates subset table for PSs*/
CREATE TABLE pss (
       PRIMARY KEY(ps_id),
       ps_id           INT           NOT NULL
	               REFERENCES equipment (equipment_id)
		       ON DELETE CASCADE, -- If equipment is deleted, delete subset as well
       ps_range        VARCHAR(50) -- In form of ###-###
);


/*Creates rule restricting deletion from the PSs table*/
CREATE RULE ps_id_restrict AS -- If a subset record is deleted, do not allow if still in equipment table
    ON DELETE TO pss
 WHERE (ps_id IN (SELECT equipment_id
                    FROM equipment))
    DO INSTEAD NOTHING;


/*Inserts sample data from equipment into pss*/
INSERT INTO pss
VALUES (5,'25-75');


/*Create subset table for Cal Racks*/
CREATE TABLE cal_racks (
       PRIMARY KEY(cal_rack_id),
       cal_rack_id         INT          NOT NULL
	                   REFERENCES equipment (equipment_id)
		           ON DELETE CASCADE, -- If equipment is deleted, delete subset as well
       cal_rack_size       VARCHAR(20)  NOT NULL
);

/*Create rule restricting deletion from the Cal Racks table*/
CREATE RULE cal_rack_id_restrict AS -- If a subset record is deleted, do not allow if still in equipment table
    ON DELETE TO cal_racks
 WHERE (cal_rack_id IN (SELECT equipment_id
                          FROM equipment))
    DO INSTEAD NOTHING;


/*Insert sample data from equipment into cal_racks*/
INSERT INTO cal_racks
VALUES (6, 'CC');


/*Creates subset table for Cal Or Sets*/
CREATE TABLE cal_or_sets (
       PRIMARY KEY(cal_or_set_id),
       cal_or_set_id                INT           NOT NULL
	                            REFERENCES equipment (equipment_id)
	                            ON DELETE CASCADE, -- If equipment is deleted, delete subset as well
       cal_or_size                  NUMERIC(4, 3) NOT NULL
			            CONSTRAINT negative_size -- Size cannot be negative
			            CHECK(cal_or_size >= 0),
																							                 cal_or_set_label             VARCHAR(10)   NOT NULL,
																								         cal_or_total_number_of_or    INT           NOT NULL
				    CONSTRAINT negative_quantity -- Number in a set cannot be negative
				    CHECK(cal_or_total_number_of_or >= 0)
);


/*Creates rule restricting deletion from the Cal Or Sets table*/
CREATE RULE cal_or_set_id_restrict AS -- If a subset record is deleted, do not allow if still in equipment table
    ON DELETE TO cal_or_sets
 WHERE (cal_or_set_id IN (SELECT equipment_id
                            FROM equipment))
    DO INSTEAD NOTHING;


/*Inserts sample data from equipment into cal_or_sets*/
INSERT INTO cal_or_sets
VALUES (6, 0.040, 'BA', 14);



/* Creates table TMpersonnels, holding info about personnels */
CREATE TABLE TMpersonnels (
    PRIMARY KEY (personnel_id),
    personnel_id            SERIAL, 
    Personnel_first_name    VARCHAR(50)   NOT NULL,
    Personnel_last_name     VARCHAR(50)   NOT NULL,
    Personnel_username      VARCHAR(50)   NOT NULL UNIQUE,
    Personnel_password      VARCHAR(256)  NOT NULL, -- crypted using pgcrypto
    Personnel_is_admin      BOOLEAN       NOT NULL -- t for admin and f for not admin
);

/* populate TMpersonnels table with sample data */
/* reference: https://www.meetspaceapp.com/2016/04/12/passwords-postgresql-pgcrypto.html */
INSERT INTO TMpersonnels (Personnel_first_name, Personnel_last_name, Personnel_username, Personnel_password, Personnel_is_admin)
VALUES ('John',  'Doe',   'johnd123',  crypt('12345', gen_salt('bf')),    't'),
       ('Alex',  'Beck',  'ABC123',    crypt('asdqwe', gen_salt('bf')),   't'),
       ('Cindy', 'Smith', 'CDohnd123', crypt('password', gen_salt('bf')), 'f');

SELECT * FROM TMpersonnels  -- test selecting user by matching username and password
    WHERE Personnel_username = 'ABC123' AND
          Personnel_password = crypt('asdqwe', Personnel_password);                      
UPDATE TMpersonnels SET Personnel_password = crypt('newpassword',gen_salt('bf')) -- test updating password
    WHERE Personnel_username = 'ABC123' AND
          Personnel_password = crypt('asdqwe', Personnel_password);
          
SELECT * FROM TMpersonnels;



/* Creates table Projects */
-- TODO: add the foreign key reactor_id from reactors table
CREATE TABLE projects (
    PRIMARY KEY (project_id),
    project_id                  SERIAL, 
    project_start_date          DATE     NOT NULL, -- generic? MM-DD-YYYY 
    project_expected_end_date   DATE,              -- generic? MM-DD-YYYY 
    project_equipment_ship_date DATE     NOT NULL, -- generic? MM-DD-YYYY 
    project_type                VARCHAR(256),
    project_testing_type        VARCHAR(256),
    project_is_active           BOOLEAN       NOT NULL, -- t for active and f for not active
    CONSTRAINT end_dates -- business rule: end_date needs to be later than start_date or ship_date
    CHECK (project_expected_end_date >= project_start_date AND project_expected_end_date >= project_equipment_ship_date)
);

INSERT INTO projects (project_start_date, project_expected_end_date, project_equipment_ship_date, project_type, project_testing_type, project_is_active)
VALUES ('09/06/2017',  NULL,           '09/10/2017',  'A',    'TESTA',   't'),
       ('09/16/2017',  '09/19/2017',   '09/10/2017',  'A123', 'TEST123', 't'),
       ('02/21/2017',  '02/27/2018',   '09/10/2017',  'B',    'TESTA123','t');

/* implement deletion rule -DENY- for the Projects table */
CREATE RULE projects_deny_deletionrule AS 
    ON DELETE TO projects DO INSTEAD
    UPDATE projects
    SET project_is_active = FALSE
    WHERE project_id = OLD.project_id;

DELETE FROM projects WHERE project_id = 3; -- test projects_deny_deletionrule constraint
SELECT * FROM projects;



/* Creates linking table ProjectsPersonnels */
CREATE TABLE projects_personnels (
    FOREIGN KEY (personnel_id)
                REFERENCES TMpersonnels (personnel_id)
                ON DELETE RESTRICT, 
    FOREIGN KEY (project_id)
                REFERENCES projects (project_id)
                ON DELETE RESTRICT, 
    personnel_id    INT     NOT NULL,
    project_id      INT     NOT NULL,
    PRIMARY KEY(personnel_id, project_id) 
);

INSERT INTO projects_personnels 
VALUES (1,1), (2,3);

SELECT CONCAT(Personnel_first_name,' ', Personnel_last_name) AS personnel_name, 
       project_id, project_start_date 
    FROM projects_personnels NATURAL JOIN projects NATURAL JOIN TMpersonnels;

-- Create table to hold clients
CREATE TABLE clients (
    PRIMARY KEY (client_id),
    client_id                    SERIAL,
    client_city                  VARCHAR(100) NOT NULL,
    client_company_name          VARCHAR(100) NOT NULL,
    client_contact_email         VARCHAR(100) NOT NULL,
    client_contact_first_name    VARCHAR(100) NOT NULL,
    client_contact_last_name     VARCHAR(100) NOT NULL,
    client_contact_phone_number  VARCHAR(20)  NOT NULL,
    client_country               VARCHAR(2)   NOT NULL, -- country code
    client_street_address        VARCHAR(50)  NOT NULL,
    client_zip_code              VARCHAR(14)  NOT NULL
);

-- Insert an example fake client
INSERT INTO clients (
  client_city,
  client_company_name,
  client_contact_email,
  client_contact_first_name,
  client_contact_last_name,
  client_contact_phone_number,
  client_country,
  client_street_address,
  client_zip_code
)
VALUES
('Danville', 'Shell', 'abc@example.com', 'Tom', 'Allen', '859-555-1234', 'US',
 '600 West Walnut', '40422');
SELECT * FROM clients; -- Select the data back out to make sure insert worked

-- Create table to hold plants that links to clients
CREATE TABLE plants (
    PRIMARY KEY (plant_id),
    FOREIGN KEY (client_id)
                REFERENCES clients (client_id)
                ON DELETE RESTRICT,
    plant_id              SERIAL,
    client_id             INT NOT NULL,
    plant_street_address  VARCHAR(100),
    plant_city            VARCHAR(100),
    plant_zip_code        VARCHAR(14),
    plant_country         CHAR(2),
    plant_name            VARCHAR(255)
);

-- Insert a fake plant
INSERT INTO plants (client_id, plant_street_address, plant_city,
  plant_zip_code, plant_country, plant_name)
VALUES
(1, '100 Main Street', 'Danville', '40422', 'US', 'Shell Plant');
SELECT * FROM plants; -- Select the data back out