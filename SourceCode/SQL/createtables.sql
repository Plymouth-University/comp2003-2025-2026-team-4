CREATE TABLE Image (
    imageID INT AUTO_INCREMENT PRIMARY KEY,
    imageSrc VARCHAR(100)
);

CREATE TABLE Partner (
    partnerID INT AUTO_INCREMENT PRIMARY KEY,
    partner_name VARCHAR(100) NOT NULL,
    logo_path VARCHAR(255) NULL,
    website_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE Event (
    eventID INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    event_location VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    booking_status ENUM('OPEN','FULL','CANCELLED') DEFAULT 'OPEN',
    booking_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE EventImage (
    eventIndex INT AUTO_INCREMENT PRIMARY KEY,
    eventID INT NOT NULL,
    imageID INT NOT NULL,
    CONSTRAINT event_event_fk
        FOREIGN KEY (eventID)
        REFERENCES Event(eventID),
    CONSTRAINT event_image_fk
        FOREIGN KEY (imageID)
        REFERENCES Image(imageID)
)ENGINE=InnoDB;

CREATE TABLE Admin (
    adminID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20) NOT NULL,
    hashedPassword VARCHAR(255) NOT NULL,
   failed_attempts INT DEFAULT 0,
   locked_until DATETIME NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE Testimonial (
    testimonialID INT AUTO_INCREMENT PRIMARY KEY,
    quote_text TEXT NOT NULL,
author_name VARCHAR(100) NOT NULL,
author_role VARCHAR(100) NULL,
is_visible TINYINT(1) DEFAULT 1,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE site_settings (
setting_key VARCHAR(100) PRIMARY KEY,
setting_value TEXT NOT NULL,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;





