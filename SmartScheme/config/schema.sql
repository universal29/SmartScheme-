-- config/schema.sql

CREATE DATABASE IF NOT EXISTS smart_scheme CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smart_scheme;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_otps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    otp_code VARCHAR(6) NOT NULL,
    type ENUM('email', 'mobile') NOT NULL,
    expires_at DATETIME NOT NULL,
    attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    dob DATE,
    gender ENUM('Male', 'Female', 'Other'),
    state VARCHAR(50),
    district VARCHAR(50),
    occupation VARCHAR(100),
    income_range VARCHAR(50), 
    category ENUM('SC', 'ST', 'OBC', 'General', 'EWS'),
    education_level VARCHAR(50),
    disability_status BOOLEAN DEFAULT FALSE,
    disability_type VARCHAR(100),
    is_farmer BOOLEAN DEFAULT FALSE,
    bpl_status BOOLEAN DEFAULT FALSE,
    completeness_score INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS schemes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    ministry VARCHAR(150),
    launch_date DATE,
    description TEXT,
    benefits TEXT,
    required_docs TEXT,
    application_steps TEXT,
    official_url VARCHAR(255),
    rules_json JSON, 
    popularity INT DEFAULT 0,
    benefit_value INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS saved_schemes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    scheme_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, scheme_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (scheme_id) REFERENCES schemes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email_or_mobile VARCHAR(150) NOT NULL,
    attempts INT DEFAULT 0,
    last_attempt DATETIME,
    locked_until DATETIME
);

-- Insert Dummy Data for Schemes to populate the discovery module
INSERT INTO schemes (name, ministry, launch_date, description, benefits, required_docs, application_steps, rules_json, popularity, benefit_value) VALUES
('PM Kisan Samman Nidhi', 'Ministry of Agriculture', '2019-02-24', 'Income support to landholding farmer families.', '₹6000 per year', 'Aadhaar, Bank Account, Land Details', 'Apply online at pmkisan.gov.in', '{"is_farmer": true}', 1500, 6000),
('Ayushman Bharat PM-JAY', 'Ministry of Health', '2018-09-23', 'Health insurance coverage for low-income citizens.', '₹5 Lakh Health Cover per family', 'Aadhaar, Ration Card', 'Visit nearest empanelled hospital with documents.', '{"bpl_status": true}', 2000, 500000),
('Post Matric Scholarship for SC/ST', 'Ministry of Social Justice', '2006-04-01', 'Financial assistance to SC/ST students for post-matriculation studies.', 'Full tuition fee waiver & maintenance allowance', 'Caste Certificate, Income Certificate, Previous Marksheet', 'Apply on National Scholarship Portal', '{"category": ["SC", "ST"], "income_max": 250000}', 1200, 20000),
('Stand-Up India Scheme', 'Ministry of Finance', '2016-04-05', 'Bank loans between 10 lakh and 1 crore to at least one SC/ST borrower and one woman borrower per bank branch for setting up a greenfield enterprise.', 'Bank loan of ₹10 Lakh to ₹1 Crore', 'Identity Proof, Business Plan, Caste/Gender verification', 'Apply at nearest bank branch.', '{"gender": ["Female"], "category": ["SC", "ST"], "age_min": 18}', 800, 1000000);
