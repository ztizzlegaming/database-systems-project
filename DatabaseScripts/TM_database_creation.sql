/*Creates TubeMaster database following the specifications laid out in our design documentation
  Team JayZ
  11/5/2018
*/
CREATE EXTENSION pgcrypto;

DROP TABLE IF EXISTS problematic_ttds;
DROP VIEW IF EXISTS all_ttds;
DROP TABLE IF EXISTS assembly_parts;
DROP TABLE IF EXISTS travelled_equipment;
DROP TABLE IF EXISTS travel_history;
DROP TABLE IF EXISTS bdspec_on_times;
DROP TABLE IF EXISTS bdspecs;

DROP TABLE IF EXISTS bdd_racks;
DROP TABLE IF EXISTS ttd_racks;
DROP TABLE IF EXISTS so_sets;
DROP TABLE IF EXISTS pss;
DROP TABLE IF EXISTS cal_racks;
DROP TABLE IF EXISTS cal_or_sets;
DROP TABLE IF EXISTS repairs;
DROP TABLE IF EXISTS equipment;

DROP TABLE IF EXISTS reactor_zones;
DROP TABLE IF EXISTS reactors;
DROP TABLE IF EXISTS units;
DROP TABLE IF EXISTS plants;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS PDSpecs;

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
          
-- SELECT * FROM TMpersonnels;



/* Creates table Projects */
-- TODO: add the foreign key reactor_id from reactors table -> added later
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

/* implement deletion rule -DENY- for the Projects table */
CREATE RULE projects_deny_deletionrule AS 
    ON DELETE TO projects DO INSTEAD
    UPDATE projects
    SET project_is_active = FALSE
    WHERE project_id = OLD.project_id;



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

-- Create table to hold units that links to plants
CREATE TABLE units (
    PRIMARY KEY (unit_id),
    FOREIGN KEY (plant_id)
                REFERENCES plants (plant_id)
                ON DELETE RESTRICT,
    unit_id    SERIAL,
    plant_id   INT NOT NULL,
    unit_name  VARCHAR(255)
);

INSERT INTO units
(plant_id, unit_name)
VALUES
(1, 'Shell Unit');
SELECT * FROM units;

-- Create table to hold reactors that link to units
CREATE TABLE reactors (
    PRIMARY KEY (reactor_id),
    FOREIGN KEY (unit_id)
                REFERENCES units (unit_id)
                ON DELETE RESTRICT,
    reactor_id                   SERIAL,
    unit_id                      INT NOT NULL,
    calibrate_ttd_to             NUMERIC(10, 3)
                                   CONSTRAINT positive_calibrate_ttd_to
                                   CHECK(calibrate_ttd_to > 0),
    calibration_orfice_size      NUMERIC(10, 3) NOT NULL
                                   CONSTRAINT positive_calibration_orfice_size
                                   CHECK(calibration_orfice_size >= 0),
    catalyst_brand               VARCHAR(255),
    catalyst_change_coordinator  VARCHAR(255),
    catalyst_size                VARCHAR(255),
    chemical                     CHAR(2),
    comments                     VARCHAR(1000),
    compressor_pressure          NUMERIC(10, 3)
                                   CONSTRAINT positive_compressor_pressure
                                   CHECK(compressor_pressure >= 0),
    expected_pressure_drop       INT NOT NULL
                                   CONSTRAINT positive_expected_pressure_drop
                                   CHECK(expected_pressure_drop >= 0),
    flow_rate                    NUMERIC(10, 3)
                                   CONSTRAINT positive_flow_rate
                                   CHECK(flow_rate > 0),
    loaded_tube_length           INT
                                   CONSTRAINT positive_loaded_tube_length
                                   CHECK(loaded_tube_length > 0),
    manifold_pressure            INT
                                   CONSTRAINT positive_manifold_pressure
                                   CHECK(manifold_pressure >= 0),
    number_of_plugs              INT
                                   CONSTRAINT positive_number_of_plugs
                                   CHECK(number_of_plugs >= 1),
    number_of_rows               INT
                                   CONSTRAINT positive_number_of_rows
                                   CHECK(number_of_rows >= 1),
    number_of_supports           INT
                                   CONSTRAINT positive_number_of_supports
                                   CHECK(number_of_supports >= 1),
    number_of_thermocouples      INT
                                   CONSTRAINT positive_number_of_thermocouples
                                   CHECK(number_of_thermocouples >= 1),
    number_of_tubes              INT
                                   CONSTRAINT positive_number_of_tubes
                                   CHECK(number_of_tubes >= 1),
    number_of_coolant_tubes      INT NOT NULL
                                   CONSTRAINT positive_number_of_coolant_tubes
                                   CHECK(number_of_coolant_tubes >= 1),
    outage                       NUMERIC(10, 3) NOT NULL
                                   CONSTRAINT positive_outage
                                   CHECK(outage >= 0),
    reactor_head                 BOOLEAN,
    reactor_loading_method       VARCHAR(255),
    reactor_manway_size          NUMERIC(10, 3)
                                   CONSTRAINT positive_reactor_manway_size
                                   CHECK(reactor_manway_size > 0),
    reactor_name                 VARCHAR(255),
    reactor_pitch                NUMERIC(10, 3)
                                   CONSTRAINT positive_reactor_pitch
                                   CHECK(reactor_pitch >= 0),
    seal_air_pressure            INT
                                   CONSTRAINT positive_seal_air_pressure
                                   CHECK(seal_air_pressure >= 0),
    sonic_up_to                  INT
                                   CONSTRAINT positive_sonic_up_to
                                   CHECK(sonic_up_to > 0),
    supply_orifice_size          NUMERIC(10, 3) NOT NULL
                                   CONSTRAINT positive_supply_orifice_size
                                   CHECK(supply_orifice_size >= 0),
    supply_pressure              INT
                                   CONSTRAINT positive_supply_pressure
                                   CHECK(supply_pressure >= 0),
    testing_type                 CHAR(2),
    thermocouple_inner_diameter  INT
                                   CONSTRAINT positive_thermocouple_inner_diameter
                                   CHECK(thermocouple_inner_diameter > 0),
    tube_inner_diameter          NUMERIC(10, 3)
                                   CONSTRAINT positive_tube_inner_diameter
                                   CHECK(tube_inner_diameter > 0),
    tube_seal_size               NUMERIC(10, 3)
                                   CONSTRAINT positive_tube_seal_size
                                   CHECK(tube_seal_size > 0),
    tube_spacing                 NUMERIC(10, 3) NOT NULL
                                   CONSTRAINT positive_tube_spacing
                                   CHECK(tube_spacing >= 0)
);

INSERT INTO reactors (unit_id, calibration_orfice_size, expected_pressure_drop,
  number_of_coolant_tubes, outage, supply_orifice_size, tube_spacing)
VALUES (1, 1.24, 5, 10, 5.3, 4.2, 7.8); -- Test something negative

INSERT INTO reactors (unit_id, calibration_orfice_size, expected_pressure_drop,
  number_of_coolant_tubes, outage, supply_orifice_size, tube_spacing)
VALUES (1, -1.24, 5, 10, 5.3, 4.2, 7.8); -- Test something negative, this won't work

SELECT * FROM reactors; -- This will only contain the first result

CREATE TABLE reactor_zones (
    PRIMARY KEY (reactor_zone_id),
    FOREIGN KEY (reactor_id)
                REFERENCES reactors (reactor_id)
                ON DELETE RESTRICT,
    reactor_zone_id SERIAL,
    reactor_id INT NOT NULL,
    reactor_zone_average_pressure_drop  NUMERIC(10, 3)
                                          CONSTRAINT positive_reactor_zone_average_pressure_drop
                                          CHECK(reactor_zone_average_pressure_drop >= 0),
    reactor_zone_equiv_orifice          NUMERIC(10, 3)
                                          CONSTRAINT positive_reactor_zone_equiv_orifice
                                          CHECK(reactor_zone_equiv_orifice >= 0),
    reactor_zone_outage                 NUMERIC(10, 3) NOT NULL
                                          CONSTRAINT positive_reactor_zone_outage
                                          CHECK(reactor_zone_outage >= 0)
);

INSERT INTO reactor_zones (reactor_id, reactor_zone_average_pressure_drop,
  reactor_zone_equiv_orifice, reactor_zone_outage)
VALUES
(1, 1.23, 4.56, 7.89);

INSERT INTO reactor_zones (reactor_id, reactor_zone_average_pressure_drop,
  reactor_zone_equiv_orifice, reactor_zone_outage)
VALUES
(1, 1.23, 4.56, -7.89); -- Try a negative, this should break

SELECT * FROM reactor_zones;

-- Create table to hold records of repairs on equipment
-- References equipment and TMpersonells tables
CREATE TABLE repairs (
    PRIMARY KEY (repair_id),
    FOREIGN KEY (equipment_id)
                REFERENCES equipment (equipment_id)
                ON DELETE RESTRICT,
    FOREIGN KEY (personnel_id)
                REFERENCES TMpersonnels (personnel_id)
                ON DELETE RESTRICT,
    repair_id         SERIAL,
    equipment_id      INT NOT NULL,
    personnel_id      INT NOT NULL,
    incident_occured  VARCHAR(1000),
    repair_date       DATE NOT NULL,
    repair_notes      VARCHAR(1000)
);

-- Insert a temporary example repair record to make sure it worked
INSERT INTO repairs (equipment_id, personnel_id, incident_occured, repair_date,
  repair_notes)
VALUES
(1, 1, 'This piece broke', '11-12-2018', 'The equipment was repaired');
SELECT * FROM repairs;


-- update constraint on projects table: add reactor_id as foreign key 
ALTER TABLE projects ADD COLUMN reactor_id INT NOT NULL;
ALTER TABLE projects
    ADD CONSTRAINT reactor_id_fkey FOREIGN KEY (reactor_id) 
    REFERENCES reactors (reactor_id)
    ON DELETE RESTRICT;

-- populate sample data...    
INSERT INTO projects (reactor_id, project_start_date, project_expected_end_date, project_equipment_ship_date, project_type, project_testing_type, project_is_active)
VALUES (1, '09/06/2017',  NULL,           '09/10/2017',  'A',    'TESTA',   't'),
       (1, '09/16/2017',  '09/19/2017',   '09/10/2017',  'A123', 'TEST123', 't'),
       (1, '02/21/2017',  '02/27/2018',   '09/10/2017',  'B',    'TESTA123','t');

DELETE FROM projects WHERE project_id = 3; -- test projects_deny_deletionrule constraint
SELECT * FROM projects;


/* create table Pressure_drop_specs*/
CREATE TABLE PDSpecs (
    PRIMARY KEY (PDSpec_id),
    FOREIGN KEY (project_id)
                REFERENCES projects (project_id)
                ON DELETE RESTRICT,
    PDSpec_id                    SERIAL,
    project_id                   INT        NOT NULL,
    -- TODO: create CONSTRAINT: TTD_number no higher than total quantity of TTD in TubeTestDevices table
    TTD_number                   INT,
    TTD_flowrate                 NUMERIC(10, 3)
                                          CONSTRAINT positive_PDSpecs_TTD_flowrate
                                          CHECK(TTD_flowrate >= 0),
    TTD_sealpressure             NUMERIC(10, 3)
                                          CONSTRAINT positive_PDSpecs_TTD_sealpressure
                                          CHECK(TTD_sealpressure >= 0),
    TTD_sealsize                 VARCHAR(255), 
    TTD_testgas                  VARCHAR(255)
);

-- populate sample data...    
INSERT INTO PDSpecs (project_id, TTD_number, TTD_flowrate, TTD_sealpressure, TTD_sealsize, TTD_testgas)
VALUES (1, 1,     3.141,  NULL,     '12',         'TESTGAS'),
       (2, 1,     0.159,  156.26,   'A123',       'TESTGAS'),
       (1, NULL,  0.92,   156.265,  'SEALSIZE12', 'TESTGAS');

SELECT * FROM PDSpecs;

/* Create the table to hold the Blowdown Specfications */
CREATE TABLE bdspecs (
    PRIMARY KEY (blowdown_spec_id),
    blowdown_spec_id                SERIAL,
    blowdown_actual_blowdown_time   NUMERIC(10,3) DEFAULT NULL
                                    CONSTRAINT negative_actual_bd_time_value -- Cannot be negative
                                    CHECK (blowdown_actual_blowdown_time >= 0),
    blowdown_final_seal_delay       NUMERIC(10,3) DEFAULT NULL
                                    CONSTRAINT negative_final_seal_delay_value -- Cannot be negative
                                    CHECK (blowdown_final_seal_delay >= 0),
    blowdown_flow_rate              NUMERIC(10,3) DEFAULT NULL
                                    CONSTRAINT negative_flow_rate_value -- Cannot be negative
                                    CHECK (blowdown_flow_rate >= 0),
    blowdown_initial_seal_delay     NUMERIC(10,3) DEFAULT NULL
                                    CONSTRAINT negative_initial_seal_delay_value -- Cannot be negative
                                    CHECK (blowdown_initial_seal_delay >= 0),
    blowdown_off_time               NUMERIC(10,3) DEFAULT NULL
                                    CONSTRAINT negative_vd_off_time_value -- Cannot be negative
                                    CHECK (blowdown_off_time >= 0),
    blowdown_pressure               NUMERIC(10,3) DEFAULT NULL
                                    CONSTRAINT negative_bd_pressure_value -- Cannot be negative
                                    CHECK (blowdown_pressure >= 0),
    blowdown_regulator_pressure     NUMERIC(10,3) DEFAULT NULL
                                    CONSTRAINT negative_bd_regulator_pressure_value -- Cannot be negative
                                    CHECK (blowdown_regulator_pressure >= 0),
    blowdown_seal_pressure          NUMERIC(10,3) DEFAULT NULL
                                    CONSTRAINT negative_seal_pressure_value -- Cannot be negative
                                    CHECK (blowdown_seal_pressure >= 0),
    number_blowdown_rigs            INT DEFAULT NULL
                                    CONSTRAINT negative_num_bd_rigs_value -- Cannot be negative
                                    CHECK (number_blowdown_rigs >= 0),
    total_blowdown_time             NUMERIC(10,3) DEFAULT NULL
                                    CONSTRAINT negative_total_bd_time_value -- Cannot be negative
                                    CHECK (blowdown_seal_pressure >= 0),
    project_id                      INT NOT NULL
                                    REFERENCES projects (project_id)
    );

/* Insert sample data into bdspecs */
INSERT INTO bdspecs (blowdown_actual_blowdown_time, blowdown_final_seal_delay, blowdown_flow_rate, blowdown_initial_seal_delay, blowdown_off_time, blowdown_pressure, blowdown_regulator_pressure, blowdown_seal_pressure, number_blowdown_rigs, total_blowdown_time, project_id)
VALUES (1.1, 0.5, 15.22, 0.01, 110.2, 1.0, 18.8, 2, 211.0, 123.567, 1),
       (12.1, 10.5, 25.23, 1.21, 0.24, 2.2, 111.11, 1.201, 20, 23.0, 2);

/* Create the bdspec_on_times table */
CREATE TABLE bdspec_on_times (
    PRIMARY KEY (blowdown_on_id),
    blowdown_on_id              SERIAL,
    blowdown_on_time            NUMERIC(10,3) DEFAULT NULL
                                CONSTRAINT negative_bd_on_time_value -- Cannot be negative
                                CHECK (blowdown_on_time >= 0),
    blowdown_spec_id            INT NOT NULL
                                REFERENCES bdspecs (blowdown_spec_id)
    );

/* Populate the bdspec_on_times table with sample data */
INSERT INTO bdspec_on_times (blowdown_on_time, blowdown_spec_id)
VALUES (11.222, 1),
       (123.0,  2);

/* Create the Travel History table */
CREATE TABLE travel_history (
    PRIMARY KEY (travel_history_id),
    travel_history_id               SERIAL,
    reactor_id                      INT NOT NULL
                                    REFERENCES reactors (reactor_id),
    shipping_date                   DATE NOT NULL
    );

/* Insert sample data into the Travel History Table */
INSERT INTO travel_history (reactor_id, shipping_date)
VALUES (1, '2/10/2010'),
       (1, '1/1/2001');

/* Create the Travelled Equipment Table */
CREATE TABLE travelled_equipment(
    PRIMARY KEY (equipment_id, travel_history_id),
    equipment_id                    INT NOT NULL
                                    REFERENCES equipment(equipment_id),
    travel_history_id               INT NOT NULL
                                    REFERENCES travel_history (travel_history_id)
                                    ON DELETE CASCADE
    );

/* Create sample data for the Travelled Equipment Table */
INSERT INTO travelled_equipment
VALUES (1,1),
       (2,2),
       (1,2);

/* Create the Assembly Parts Table */
CREATE TABLE assembly_parts (
    PRIMARY KEY (assembly_id, part_id),
    assembly_id                     INT NOT NULL
                                    REFERENCES equipment (equipment_id),
    part_id                         INT NOT NULL
                                    REFERENCES equipment (equipment_id)
                                    
    );


/* Insert sample data into Assembly Parts */
INSERT INTO assembly_parts
VALUES (1,2);

/* Create View of all TTDs in Equipment */
CREATE VIEW all_ttds
AS
SELECT *
  FROM equipment
 WHERE equipment_name = 'TTD';

/* Create problematic_ttds table */
CREATE TABLE problematic_ttds (
    PRIMARY KEY (problem_ttd_key),
    problem_ttd_key                 SERIAL,
    equipment_id                    INT NOT NULL
                                    REFERENCES equipment (equipment_id),
    reactor_id                      INT NOT NULL
                                    REFERENCES reactors (reactor_id)
    );
