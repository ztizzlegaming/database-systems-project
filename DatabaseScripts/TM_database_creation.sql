/*Creates TubeMaster database following the specifications laid out in our design documentation
  Team JayZ
  11/5/2018
*/

/*Creates table Equipment, holding onto info of all of TMs inventory*/
CREATE TABLE equipment (
       PRIMARY KEY(equipment_id),
       equipment_id                      SERIAL,
       equipment_name                    VARCHAR(256) NOT NULL,
       equipment_sn                      INT DEFAULT NULL,
       equipment_quantity                INT NOT NULL,
       equipment_notes                   VARCHAR(200) DEFAULT NULL,
       equipment_tag                     VARCHAR(5) NOT NULL,
       equipment_location                VARCHAR(100) NOT NULL,
       equipment_shelf_location          VARCHAR(100) DEFAULT NULL,
       equipment_updates                 VARCHAR(256) DEFAULT NULL,
       equipment_inventory_update_date   DATE DEFAULT NULL,
       equipment_description             VARCHAR(500) NOT NULL,
       equipment_modifications           VARCHAR(256) DEFAULT NULL,
       equipment_in_out_of_service       INT NOT NULL,
       equipment_potential_projects      VARCHAR(1000) DEFAULT NULL,
       equipment_tubemaster_value        NUMERIC(10, 2) NOT NULL,
       equipment_shipping_value          NUMERIC(10, 2) NOT NULL,
       equipment_client_value            NUMERIC(10, 2) DEFAULT NULL,
       equipment_weight                  INT NOT NULL,
       equipment_cost                    NUMERIC(10, 2) DEFAULT NULL,
       equipment_vendor                  VARCHAR(100) DEFAULT NULL,
       equipment_manufacturer            VARCHAR(100),
       equipment_date_of_return          DATE DEFAULT NULL,
       equipment_ideal_storage_location  VARCHAR(100) DEFAULT NULL
);
       
INSERT INTO equipment (equipment_name, equipment_quantity, equipment_tag, equipment_location, equipment_description, equipment_in_out_of_service, equipment_tubemaster_value, equipment_shipping_value, equipment_weight)
VALUES ('Test Equipment', 1, 'Blue', 'Warehouse', 'This is a test equipment', 1, 1000.00, 2000.00, 75);
