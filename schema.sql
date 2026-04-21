CREATE DATABASE IF NOT EXISTS smart_scheme_db;
USE smart_scheme_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS saved_schemes;
DROP TABLE IF EXISTS user_interests;
DROP TABLE IF EXISTS schemes;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mobile VARCHAR(15) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    address TEXT,
    state VARCHAR(50),
    religion VARCHAR(50),
    occupation VARCHAR(100),
    age INT,
    annual_income DECIMAL(15,2),
    category VARCHAR(50), -- SC/ST/OBC/General
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_interests (
    user_id INT,
    interest VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, interest)
);

CREATE TABLE IF NOT EXISTS schemes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100) NOT NULL, -- Education, Health, Agriculture, Women, Senior Citizen, Employment
    state VARCHAR(50) DEFAULT 'All', -- State specific or 'Central'/'All'
    min_age INT DEFAULT 0,
    max_age INT DEFAULT 200,
    max_income DECIMAL(15,2) DEFAULT NULL, -- NULL means no income limit
    target_category VARCHAR(100) DEFAULT 'All', -- SC/ST/OBC/General or 'All'
    gender ENUM('All', 'Male', 'Female') DEFAULT 'All',
    apply_url VARCHAR(255) DEFAULT 'https://india.gov.in',
    required_documents TEXT,
    ai_eligibility_summary TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS saved_schemes (
    user_id INT,
    scheme_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (scheme_id) REFERENCES schemes(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, scheme_id)
);

-- Insert a default Admin user
INSERT IGNORE INTO users (full_name, email, mobile, password_hash, role) 
VALUES ('System Admin', 'admin@smartscheme.gov', '0000000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); 
-- password is 'password'

-- Insert Sample Schemes
INSERT IGNORE INTO schemes (name, description, category, state, min_age, max_age, max_income, target_category, gender) VALUES
('Mukhyamantri Yuva Swavalamban Yojana', 'Scholarship scheme for meritorious students of Gujarat.', 'Education', 'Gujarat', 16, 25, 600000.00, 'All', 'All'),
('Pradhan Mantri Kisan Samman Nidhi (PM-KISAN)', 'Income support scheme for farmers.', 'Agriculture', 'Central', 18, 200, NULL, 'All', 'All'),
('Sukanya Samriddhi Yojana', 'A small deposit scheme for the girl child as part of the Beti Bachao Beti Padhao campaign.', 'Women', 'Central', 0, 10, NULL, 'All', 'Female'),
('National Overseas Scholarship', 'Provides financial assistance to selected students for pursuing Master level courses and Ph.D abroad.', 'Education', 'Central', 18, 35, 800000.00, 'SC', 'All'),
('Ayushman Bharat Yojana', 'A national public health insurance fund of the Government of India that aims to provide free access to health insurance coverage for low income earners in the country.', 'Health', 'Central', 0, 200, 250000.00, 'All', 'All'),
('Gujarat Senior Citizen Pension Scheme', 'Financial assistance to destitute senior citizens.', 'Senior Citizen', 'Gujarat', 60, 200, 150000.00, 'All', 'All'),
('Stand Up India Scheme', 'Facilitates bank loans between 10 lakh and 1 crore to at least one Scheduled Caste (SC) or Scheduled Tribe (ST) borrower and at least one woman borrower per bank branch for setting up a greenfield enterprise.', 'Employment', 'Central', 18, 200, NULL, 'SC/ST', 'All'),
('Digital Gujarat Scholarship Scheme', 'Financial assistance to students of financially weaker sections of Gujarat across school and college levels.', 'Education', 'Gujarat', 10, 30, 250000.00, 'OBC/SC/ST', 'All'),
('CM Scholarship Scheme (CMSS)', 'State government academic financial aid for meritorious underprivileged undergraduate students in Gujarat.', 'Education', 'Gujarat', 17, 25, 450000.00, 'All', 'All'),
('Maharashtra Rajarshi Shahu Maharaj Scholarship', 'Financial assistance to economically backward class students for higher education.', 'Education', 'Maharashtra', 18, 25, 800000.00, 'All', 'All'),
('Mahatma Jyotirao Phule Jan Arogya Yojana', 'Health insurance scheme for families below the poverty line in Maharashtra.', 'Health', 'Maharashtra', 0, 200, 250000.00, 'All', 'All'),
('Karnataka Vidyasiri Scheme', 'Provides free hostel accommodation and scholarship to minority students.', 'Education', 'Karnataka', 15, 30, 200000.00, 'All', 'All'),
('Krishi Bhagya Scheme', 'Aims to improve rainfed agriculture farming with efficient water management.', 'Agriculture', 'Karnataka', 18, 200, NULL, 'All', 'All'),
('Dr. Muthulakshmi Reddy Maternity Benefit Scheme', 'Financial assistance to poor pregnant women in Tamil Nadu.', 'Women', 'Tamil Nadu', 18, 50, NULL, 'All', 'Female'),
('Moovalur Ramamirtham Ammaiyar Higher Education Assurance', 'Provides financial assistance to girls pursuing higher education in Tamil Nadu.', 'Education', 'Tamil Nadu', 17, 25, NULL, 'All', 'Female'),
('Chief Minister Kanya Sumangala Yojana', 'Financial aid scheme for the girl child in Uttar Pradesh to promote female education and health.', 'Women', 'Uttar Pradesh', 0, 21, 300000.00, 'All', 'Female'),
('UP Agriculture Equipment Subsidy', 'Subsidy on the purchase of agricultural equipment for farmers.', 'Agriculture', 'Uttar Pradesh', 18, 200, NULL, 'All', 'All'),
('Rajasthan Bhamashah Swasthya Bima Yojana', 'Cashless healthcare scheme for families covered under the National Food Security Act.', 'Health', 'Rajasthan', 0, 200, NULL, 'All', 'All'),
('Mukhyamantri Rajshree Yojana', 'Encourages the birth of girl children and supports their education in Rajasthan.', 'Women', 'Rajasthan', 0, 25, NULL, 'All', 'Female'),
('Bihar Post Matric Scholarship', 'Financial assistance to SC, ST, OBC, and EBC students studying at post-matriculation or post-secondary stage.', 'Education', 'Bihar', 15, 30, 250000.00, 'OBC/SC/ST', 'All'),
('Kerala Karunya Health Scheme', 'Health care scheme aimed at providing financial assistance for the treatment of severe diseases for poor people.', 'Health', 'Kerala', 0, 200, 300000.00, 'All', 'All'),
('Indira Gandhi National Old Age Pension Scheme', 'Monthly pension to persons above 60 years belonging to below poverty line households.', 'Senior Citizen', 'Central', 60, 200, NULL, 'All', 'All'),
('PM Employment Generation Programme (PMEGP)', 'Credit-linked subsidy program aimed at generating self-employment opportunities through establishment of micro-enterprises.', 'Employment', 'Central', 18, 200, NULL, 'All', 'All');

INSERT IGNORE INTO schemes (name, description, category, state, min_age, max_age, max_income, target_category, gender) VALUES
-- Gujarat
('Mukhyamantri Kanya Kelavani Nidhi', 'Financial assistance to girls for pursuing higher education in medical and related fields.', 'Education', 'Gujarat', 17, 25, NULL, 'All', 'Female'),
('Post Matric Scholarship for ST Students', 'Educational assistance to Scheduled Tribe students studying at post-matriculation stages.', 'Education', 'Gujarat', 15, 30, 250000.00, 'ST', 'All'),
('Vidyalaxmi Yojana', 'Incentive scheme offering bonds to girls enrolling in primary education to prevent dropouts.', 'Education', 'Gujarat', 5, 10, NULL, 'All', 'Female'),
('Swaraj Ashram Scholarship', 'Scholarship provided for children of socially and educationally backward classes for primary education.', 'Education', 'Gujarat', 6, 14, 150000.00, 'OBC/SC', 'All'),

-- Maharashtra
('Eklavya Scholarship', 'Financial assistance for post-graduate students scoring high marks in graduation.', 'Education', 'Maharashtra', 20, 30, 75000.00, 'All', 'All'),
('Dr. Punjabrao Deshmukh Vasatigruh Nirvah Bhatta Yojna', 'Provides maintenance allowance for students studying in professional courses and staying in hostels.', 'Education', 'Maharashtra', 17, 25, 800000.00, 'All', 'All'),
('State Government Open Merit Scholarship', 'Merit-based financial award for high-performing students entering junior college.', 'Education', 'Maharashtra', 15, 18, NULL, 'All', 'All'),
('Savitribai Phule Scholarship', 'Provides academic support for girls studying in specific underprivileged categories.', 'Education', 'Maharashtra', 10, 16, NULL, 'SC/ST/OBC', 'Female'),

-- Rajasthan
('Chief Minister Higher Education Scholarship', 'Provides financial help to meritorious students whose families have low income for higher studies.', 'Education', 'Rajasthan', 17, 25, 250000.00, 'All', 'All'),
('Anuprati Coaching Yojna', 'Free coaching for economically weaker section students preparing for competitive exams like civil services, engineering, and medical.', 'Education', 'Rajasthan', 16, 30, 800000.00, 'SC/ST/OBC/EWS', 'All'),
('Gargi Puraskar Yojana', 'Cash prize for girls who score high marks in class 10th and 12th board exams to encourage continuous education.', 'Education', 'Rajasthan', 15, 18, NULL, 'All', 'Female'),
('Devnarayan Gurukul Yojana', 'Scheme providing free education, boarding, and lodging to students from specialized backward classes in top private schools.', 'Education', 'Rajasthan', 10, 16, 200000.00, 'OBC/SBC', 'All'),
('Kalibai Bheel Medhavi Chhatra Scooty Yojana', 'Provides free scootys to meritorious girls pursuing college education to facilitate their commute.', 'Education', 'Rajasthan', 17, 22, 250000.00, 'All', 'Female');

INSERT IGNORE INTO schemes (name, description, category, state, min_age, max_age, max_income, target_category, gender) VALUES
-- Central
('Mahatma Gandhi National Rural Employment Guarantee Act (MGNREGA)', 'Ensures 100 days of wage employment in a financial year to a rural household whose adult members volunteer to do unskilled manual work.', 'Employment', 'Central', 18, 200, NULL, 'All', 'All'),
('Deen Dayal Upadhyaya Grameen Kaushalya Yojana (DDU-GKY)', 'Aims to transform rural poor youth into an economically independent and globally relevant workforce.', 'Employment', 'Central', 15, 35, NULL, 'SC/ST/Minority', 'All'),
('Pradhan Mantri Kaushal Vikas Yojana (PMKVY)', 'Flagship scheme for skill training of youth to be implemented by the new Ministry of Skill Development and Entrepreneurship.', 'Employment', 'Central', 18, 45, NULL, 'All', 'All'),
('Start-up India', 'Initiative aiming to build a strong eco-system for nurturing innovation and Startups in the country that will drive sustainable economic growth and generate large scale employment opportunities.', 'Employment', 'Central', 18, 200, NULL, 'All', 'All'),
('PM SVANidhi', 'Micro-credit facility scheme for providing affordable loans to street vendors.', 'Employment', 'Central', 18, 200, NULL, 'All', 'All'),
('National Apprenticeship Promotion Scheme (NAPS)', 'Scheme to promote apprenticeship training and incentivize employers who wish to engage apprentices.', 'Employment', 'Central', 14, 200, NULL, 'All', 'All'),
('Aatmanirbhar Bharat Rojgar Yojana', 'Scheme to incentivize creation of new employment opportunities during the COVID recovery phase.', 'Employment', 'Central', 18, 58, 180000.00, 'All', 'All'),

-- Gujarat
('Mukhyamantri Apprentice Yojana', 'Provides stipend to youth undergoing apprenticeship training in various establishments under the state government.', 'Employment', 'Gujarat', 18, 35, NULL, 'All', 'All'),
('Manav Kalyan Yojana', 'Provides additional tools and equipment to backward class artisans to generate more income.', 'Employment', 'Gujarat', 16, 60, 120000.00, 'All', 'All'),
('Vajpayee Bankable Yojana', 'Provides financial assistance to the craftsmen of cottage industries through nationalized banks, cooperative banks, public sector banks or private banks.', 'Employment', 'Gujarat', 18, 65, NULL, 'All', 'All'),
('Dattopant Thengadi Kariagar Vyaj Sahay Yojana', 'Provides interest subvention on loans taken by artisans and handicraft workers.', 'Employment', 'Gujarat', 18, 200, NULL, 'All', 'All'),
('Jyoti Gramodyog Vikas Yojana', 'Aims to generate employment in rural areas by facilitating the establishment of micro and small village industries.', 'Employment', 'Gujarat', 18, 55, NULL, 'All', 'All'),

-- Maharashtra
('Annasaheb Patil Arthik Magas Vikas Mahamandal Scheme', 'Provides financial help to economically weaker youth for setting up new businesses.', 'Employment', 'Maharashtra', 18, 45, 800000.00, 'General', 'All'),
('Pramod Mahajan Kaushalya UI Abhiyan', 'Focuses on integrating skill development with entrepreneurship to boost employment for youth in Maharashtra.', 'Employment', 'Maharashtra', 15, 45, NULL, 'All', 'All'),
('Chief Minister Employment Generation Programme (CMEGP)', 'A state-level initiative aimed at generating micro-enterprises and ensuring youth employment.', 'Employment', 'Maharashtra', 18, 45, NULL, 'All', 'All'),
('Maharashtra State Rural Livelihoods Mission', 'Alleviating rural poverty by building sustainable institutions for the poor, specifically women.', 'Employment', 'Maharashtra', 18, 60, 150000.00, 'All', 'Female'),

-- Rajasthan
('Mukhyamantri Yuva Sambal Yojana', 'Provides monthly unemployment allowance to educated unemployed youth in Rajasthan.', 'Employment', 'Rajasthan', 21, 30, 200000.00, 'All', 'All'),
('Rajiv Gandhi Krishak Sathi Sahayata Yojana', 'Financial assistance to farmers and agricultural laborers in case of accident or death during agricultural operations.', 'Employment', 'Rajasthan', 18, 60, NULL, 'All', 'All'),
('Mukhyamantri Laghu Udyog Protsahan Yojana', 'Facilitates setting up up of new enterprises and expanding existing ones by providing interest subvention on loans.', 'Employment', 'Rajasthan', 18, 60, NULL, 'All', 'All'),
('Rajasthan Silicosis Policy', 'Provides financial assistance and rehabilitation for mine workers affected by silicosis occupational hazards.', 'Employment', 'Rajasthan', 18, 200, NULL, 'All', 'All');
