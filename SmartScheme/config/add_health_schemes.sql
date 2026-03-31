USE smart_scheme;

INSERT INTO schemes (name, description, ministry, benefits, rules_json, popularity, module_category) VALUES
(
    'Ayushman Bharat scheme (PM-JAY)', 
    'A national public health insurance fund that aims to provide free access to health insurance coverage for low income earners in the country. It is the world''s largest government sponsored healthcare program.', 
    'Ministry of Health and Family Welfare', 
    'Health cover of up to ₹5 lakh per family per year for secondary and tertiary care hospitalization.', 
    '{"age_min": 0, "income_max": 250000, "categories": ["General", "OBC", "SC", "ST"]}', 
    99, 
    'Healthcare'
),
(
    'Pradhan Mantri Surakshit Matritva Abhiyan', 
    'Program to provide guaranteed, comprehensive and quality antenatal care, free of cost, universally to all pregnant women on the 9th of every month.', 
    'Ministry of Health and Family Welfare', 
    'Free antenatal check-ups, diagnostic services, medicines, and nutritional supplements during pregnancy.', 
    '{"gender": "Female", "age_min": 18, "age_max": 50}', 
    85, 
    'Healthcare'
),
(
    'National Health Mission (NHM)', 
    'Encompasses its two Sub-Missions, the National Rural Health Mission (NRHM) and the National Urban Health Mission (NUHM), providing universal access to equitable, affordable, and quality healthcare services that are accountable and responsive to people''s needs.', 
    'Ministry of Health and Family Welfare', 
    'Subsidized localized treatments, maternal care, child healthcare facilities, and communicable disease prevention.', 
    '{"age_min": 0, "income_max": 500000}', 
    90, 
    'Healthcare'
),
(
    'Pradhan Mantri Jan Aushadhi Yojana (PMBJP)', 
    'A central initiative to provide quality medicines at affordable prices to the masses through special pharmaceutical outlets known as Pradhan Mantri Bhartiya Jan Aushadhi Kendras.', 
    'Ministry of Chemicals and Fertilizers', 
    'Access to 50%-90% drastically cheaper generic medicines and essential surgical items.', 
    '{"age_min": 0}', 
    88, 
    'Healthcare'
);
