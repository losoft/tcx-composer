DROP TABLE workouts;

CREATE TABLE workouts (
  id varchar(36) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  name varchar(128) DEFAULT 'undefined' NOT NULL,
  track LONGTEXT,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

INSERT INTO workouts (id, name) VALUES ('10297fff-c48c-4782-a475-c98e7bf7a8fd', 'Polar A360 track');
INSERT INTO workouts (id, name) VALUES ('7b7b311d-bf56-4bf6-9270-36c96f1c1b1d', 'Endomondo Tracker track');
INSERT INTO workouts (id, name) VALUES ('5cbbd3c1-5a59-4b02-a261-b585a2d921de', 'Trening cross-fit');

SELECT * FROM workouts ORDER BY creation_timestamp;
