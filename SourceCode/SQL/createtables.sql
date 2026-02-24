CREATE TABLE Image (
    imageID INT AUTO_INCREMENT PRIMARY KEY,
    imageSrc VARCHAR(100)
);

CREATE TABLE Partner (
    sponsorID INT AUTO_INCREMENT PRIMARY KEY,
    imageID INT NOT NULL,
    sponsorName VARCHAR(100) NOT NULL,
    CONSTRAINT sponsorFK
        FOREIGN KEY (imageID)
        REFERENCES Image(imageID)
);

CREATE TABLE Event (
    eventID INT AUTO_INCREMENT PRIMARY KEY,
    eventName VARCHAR(50) NOT NULL,
    eventLocation VARCHAR(200) NOT NULL,
    eventDate DATE NOT NULL,
    eventTime TIME NOT NULL
    showOnPast BOOLEAN,
    showOnFuture BOOLEAN
);

CREATE TABLE EventImages (
    eventIndex INT AUTO_INCREMENT PRIMARY KEY,
    eventID INT NOT NULL,
    imageID INT NOT NULL,
    CONSTRAINT event_event_fk
        FOREIGN KEY (eventID)
        REFERENCES Event(eventID),
    CONSTRAINT event_image_fk
        FOREIGN KEY (imageID)
        REFERENCES Image(imageID)
);

CREATE TABLE Admin (
    adminID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20) NOT NULL UNIQUE,
    hashedPassword VARCHAR(255) NOT NULL,
    email VARCHAR(100)
);

CREATE TABLE Testimonial (
    testimonialID INT AUTO_INCREMENT PRIMARY KEY,
    authorName VARCHAR(50) NOT NULL,
    testimonialText VARCHAR(500) NOT NULL,
    showOnPage BOOLEAN DEFAULT FALSE
);


