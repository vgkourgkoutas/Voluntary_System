DROP DATABASE IF EXISTS voluntary_system;
CREATE DATABASE voluntary_system;
USE voluntary_system;

/*Πίνακας με τον οποίο αποθηκεύουμε τα στοιχεία των χρηστών του συστήματος*/
CREATE TABLE IF NOT EXISTS allusers (
  user_id INT AUTO_INCREMENT NOT NULL,
  user_username VARCHAR(20) UNIQUE NOT NULL,
  user_password VARCHAR(20) NOT NULL,
  user_role ENUM('admin', 'rescuer', 'citizen') NOT NULL,
  PRIMARY KEY (user_id)
) ENGINE=INNODB;

/*Πίνακας με τον οποίο αποθηκεύουμε τις γεωγραφικές συντεταγμένες της βάσης*/
CREATE TABLE IF NOT EXISTS base (
  base_id INT NOT NULL,
  base_latitude FLOAT,
  base_longitude FLOAT,
  PRIMARY KEY (base_id)
) ENGINE=INNODB;

/*Πίνακας με τον οποίο αποθηκεύουμε τις πληροφορίες για τους διασώστες*/
CREATE TABLE IF NOT EXISTS rescuers (
  rescuer_id INT NOT NULL,
  rescuer_username VARCHAR(20) NOT NULL,
  rescuer_vehicle VARCHAR(50) NOT NULL,
  PRIMARY KEY (rescuer_id),
  CONSTRAINT RESCID FOREIGN KEY (rescuer_id) REFERENCES allusers(user_id) 
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB;

/*Πίνακας με τον οποίο αποθηκεύουμε τα στοιχεία των πολιτών*/
CREATE TABLE IF NOT EXISTS citizen_registration (
  citizen_id INT AUTO_INCREMENT NOT NULL,
  citizen_username VARCHAR(20) UNIQUE NOT NULL,
  citizen_password VARCHAR(20) NOT NULL,
  citizen_name VARCHAR(100) NOT NULL,
  citizen_phone BIGINT NOT NULL,
  citizen_latitude DECIMAL(10, 8),
  citizen_longitude DECIMAL(10, 8),
  PRIMARY KEY (citizen_id),
  CONSTRAINT CITZID FOREIGN KEY (citizen_id) REFERENCES allusers(user_id) 
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB;

/*Πίνακας με τον οποίο αποθηκεύουμε τις ανακοινώσεις που δημοσιεύονται από τους διαχειριστές.*/
CREATE TABLE IF NOT EXISTS admin_announcements (
  ann_id INT AUTO_INCREMENT NOT NULL,
  ann_announcement TEXT,
  PRIMARY KEY (ann_id)
) ENGINE=INNODB;

/*Πίνακας με τον οποίο αποθηκεύουμε τις κατηγορίες των αντικειμένων που σχετίζονται με tasks.*/
CREATE TABLE IF NOT EXISTS add_category (
  category_id INT AUTO_INCREMENT NOT NULL,
  category_items VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_bin UNIQUE NOT NULL,
  category_date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (category_id)
) ENGINE=INNODB;

/*Πίνακας με τον οποίο αποθηκεύουμε τα αντικείμενα που μπορεί να ζητηθούν ή να προσφερθούν*/
CREATE TABLE IF NOT EXISTS items (
  item_id INT AUTO_INCREMENT NOT NULL,
  item_stuff VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin UNIQUE NOT NULL,
  item_quantity INT,
  item_category_id INT,
  item_category VARCHAR(30) NOT NULL,
  PRIMARY KEY (item_id, item_stuff),
  CONSTRAINT ITEMCATID FOREIGN KEY (item_category_id) REFERENCES add_category(category_id) 
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB;

/*Πίνακας με τον οποίο αποθηκεύουμε αιτήματα από πολίτες*/
CREATE TABLE IF NOT EXISTS citizen_requests (
  citres_id INT AUTO_INCREMENT PRIMARY KEY,
  citres_citizen_id INT,
  citres_stuff VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  citres_people INT NOT NULL,
  citres_date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  citres_state ENUM('ACCEPTED', 'NOT ACCEPTED') DEFAULT 'NOT ACCEPTED' NOT NULL,
  CONSTRAINT CITRESCIT FOREIGN KEY (citres_citizen_id) REFERENCES citizen_registration(citizen_id) 
  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT CTZSTFF FOREIGN KEY (citres_stuff) REFERENCES items(item_stuff)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB;

/*Πίνακας με τον οποίο αποθηκεύουμε προσφορές από πολίτες,*/
CREATE TABLE IF NOT EXISTS citizen_offers (
  citoff_id INT AUTO_INCREMENT PRIMARY KEY,
  citoff_citizen_id INT,
  citoff_stuff VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  citoff_quantity INT NOT NULL,
  citoff_date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  citoff_state ENUM('ACCEPTED', 'NOT ACCEPTED') DEFAULT 'NOT ACCEPTED' NOT NULL,
  CONSTRAINT CTZOFFCTZ FOREIGN KEY (citoff_citizen_id) REFERENCES citizen_registration(citizen_id) 
  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT CTZOFFSTF FOREIGN KEY (citoff_stuff) REFERENCES items(item_stuff)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB;

/*Πίνακας με τον οποίο αποθηκεύουμε τα τρέχοντα tasks που έχουν ανατεθεί σε διασώστες*/
CREATE TABLE IF NOT EXISTS current_tasks (
  task_id INT AUTO_INCREMENT NOT NULL,
  task_rescuer_id INT,
  citizen_id INT,
  citizen_fullname VARCHAR(100) NOT NULL,
  citizen_telephone BIGINT NOT NULL,
  offer_request_date_added TIMESTAMP,
  task_date_received TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  task_date_completed TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  item_stuff VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  item_quantity INT,
  task_latitude DECIMAL(10, 8),
  task_longitude DECIMAL(10, 8),
  task_type ENUM('request', 'offer') NOT NULL,
  PRIMARY KEY(task_id),
  CONSTRAINT CURRTSKRESC FOREIGN KEY (task_rescuer_id) REFERENCES rescuers(rescuer_id) 
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB;

/*Πίνακας με τον οποίο αποθηκεύουμε παλιά αιτήματα*/
CREATE TABLE IF NOT EXISTS old_requests (
  oldres_id INT AUTO_INCREMENT PRIMARY KEY,
  oldres_citizen_id INT,
  oldres_stuff VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  oldres_people INT NOT NULL,
  oldres_date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB;

/*Πίνακας με τον οποίο αποθηκεύουμε παλιές προσφορές*/
CREATE TABLE IF NOT EXISTS old_offers (
  oldoff_id INT AUTO_INCREMENT PRIMARY KEY,
  oldoff_citizen_id INT,
  oldoff_stuff VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  oldoff_quantity INT NOT NULL,
  oldoff_date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB;
/*Πίνακας με τον οποίο αποθηκεύουμε τα στοιχεία των οχημάτων*/
CREATE TABLE IF NOT EXISTS vehicles (
  vehicle_rescuer_id INT,
  vehicle_name VARCHAR(50) NOT NULL,
  vehicle_tasks INT DEFAULT 0,
  vehicle_latitude FLOAT,
  vehicle_longitude FLOAT,
  CONSTRAINT VEHRESID FOREIGN KEY (vehicle_rescuer_id) REFERENCES rescuers(rescuer_id) 
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB;

/*Πίνακας με τον οποίο αποθηκεύουμε τα αντικείμενα που είναι φορτωμένα στο όχημα */ 
CREATE TABLE IF NOT EXISTS vehicle_storage (
  vehicle_rescuer_id INT,
  item_id INT,
  item_name VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  item_quantity INT,
  PRIMARY KEY (vehicle_rescuer_id, item_id),
  CONSTRAINT VEHIRESID FOREIGN KEY (vehicle_rescuer_id) REFERENCES rescuers(rescuer_id) 
  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT VEHITNAM FOREIGN KEY (item_id) REFERENCES items(item_id) 
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB;

/*Πίνακας με τον οποίον καταγράφουμε τις συνδέσεις μεταξύ ενός διασώστη και των γεωγραφικών συντεταγμένων που σχετίζονται με tasks.*/
CREATE TABLE task_connections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rescuer_id INT,
    task_latitude DECIMAL(10, 8),
    task_longitude DECIMAL(10, 8),
    vehicle_latitude DOUBLE,
    vehicle_longitude DOUBLE,
    task_type VARCHAR(50),
    FOREIGN KEY (rescuer_id) REFERENCES rescuers(rescuer_id)
);


INSERT INTO allusers VALUES
( NULL, 'Bill', 'Bill1', 'admin');


INSERT INTO base VALUES
(1,38.24618, 21.73514);

