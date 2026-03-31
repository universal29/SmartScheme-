USE smart_scheme;

-- Safely add the column if it doesn't exist
SET @dbname = DATABASE();
SET @tablename = 'schemes';
SET @columnname = 'state';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname, " VARCHAR(100) DEFAULT 'All India'")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Update existing schemes to have All India as state if NULL
UPDATE schemes SET state = 'All India' WHERE state IS NULL;

INSERT INTO schemes (name, description, ministry, benefits, rules_json, popularity, module_category, state) VALUES
(
    'Central Sector Scheme of Scholarship for College and University Students', 
    'Provides financial assistance to meritorious students from low-income families to meet part of their day-to-day expenses while pursuing higher studies.', 
    'Ministry of Education', 
    '₹12,000 per annum at Graduation level for first three years of College and University courses.', 
    '{"age_max": 25, "income_max": 450000}', 
    95, 
    'Education',
    'All India'
),
(
    'National Means-cum-Merit Scholarship (NMMS)', 
    'Awards scholarships to meritorious students of economically weaker sections to arrest their drop out at class VIII and encourage them to continue study at secondary stage.', 
    'Ministry of Education', 
    'Scholarship amount of ₹12,000 per annum for study in classes IX to XII.', 
    '{"age_max": 18, "income_max": 350000}', 
    88, 
    'Education',
    'All India'
),
(
    'Rajarshi Shahu Maharaj Merit Scholarship', 
    'A state-level scholarship aimed at providing financial aid specifically to Scheduled Caste (SC) students who secure high marks in the SSC examination.', 
    'Department of Social Justice', 
    'Monthly stipend of ₹300 for two years (Class XI and XII).', 
    '{"categories": ["SC"], "state": "Maharashtra"}', 
    75, 
    'Education',
    'Maharashtra'
),
(
    'Chief Minister''s Merit Scholarship Scheme', 
    'A state scholarship designed to reward academic excellence in higher secondary education.', 
    'Directorate of Higher Education', 
    'One-time financial reward of ₹10,000.', 
    '{"age_max": 20}', 
    82, 
    'Education',
    'Delhi'
);
