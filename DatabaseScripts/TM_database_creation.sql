/*Creates TubeMaster database following the specifications laid out in our design documentation
  Team JayZ
  11/5/2018
*/

DROP TABLE bdd_racks;
DROP TABLE equipment;

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
					        'green','GREEN', 'N/A')),
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
       
INSERT INTO equipment (equipment_name, equipment_quantity, equipment_tag, equipment_location,
                       equipment_description, equipment_in_out_of_service,
		       equipment_tubemaster_value, equipment_shipping_value, equipment_weight,
		       equipment_manufacturer)
VALUES ('Test Equipment', 1, 'Blue', 'Warehouse', 'This is a test equipment', 1, 2000.0,
        2000.0, 75, 'Us'),
       ('BDD Rack', 1, 'Red', 'Saudi', 'This is a BDD rack', 0, 1500.00, 2500.00, 20, 'Us');

CREATE TABLE bdd_racks (
       PRIMARY KEY(bdd_rack_id),
       bdd_rack_id         INT          NOT NULL
       		           REFERENCES equipment (equipment_id)
			   ON DELETE CASCADE, -- If equipment is deleted, delete subset as well
       bdd_tube_rack_size  VARCHAR(20)  NOT NULL
);

CREATE RULE bdd_rack_id_restrict AS -- If a subset record is deleted, do not allow if still in equipment table
    ON DELETE TO bdd_racks
 WHERE (bdd_rack_id IN (SELECT equipment_id
                          FROM equipment))
    DO INSTEAD NOTHING;

INSERT INTO bdd_racks
VALUES (2, 'AAA');
