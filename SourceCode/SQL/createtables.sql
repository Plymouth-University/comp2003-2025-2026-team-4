CREATE TABLE Sponsor(
    sponsorID INT IDENTITY(1,1) PRIMARY KEY,
    imageID INT NOT NULL, 
    sponsorName VARCHAR(100) NOT NULL
    CONSTRAINT sponsorFK
    FOREIGN KEY (imageID)
    REFERENCES Image(imageID)

);


CREATE TABLE Event(
    eventID INT IDENTITY(1,1) PRIMARY KEY,
    eventName VARCHAR(50) NOT NULL,
    eventLocation VARCHAR(200) NOT NULL,
    eventDate DATE NOT NULL,
    eventTime TIME NOT NULL
);

CREATE TABLE EventImages(
    eventID INT PRIMARY KEY,
    eventIndex INT IDENTITY (1,1) PRIMARY KEY,
    imageID INT NOT NULL
    CONSTRAINT eventFK
    FOREIGN KEY (imageID)
    REFERENCES Image(imageID)
    CONSTRAINT eventFK
    FOREIGN KEY (eventID)
    REFERENCES Event(eventID)
);

CREATE TABLE Admin(
    adminID INT IDENTITY(1,1) PRIMARY KEY,
    username VARCHAR(20) NOT NULL,
    hashedPassword VARCHAR(50) NOT NULL,
    email VARCHAR
);

CREATE TABLE Testimonial(
    testimonialID INT IDENTITY(1,1) PRIMARY KEY,
    authorName VARCHAR(50) NOT NULL,
    testimonialText VARCHAR(500) NOT NULL
);

CREATE TABLE Image(
    imageID INT PRIMARY KEY,
    imageSrc VARCHAR(100)
);