USE smart_scheme;

INSERT INTO schemes (name, description, ministry, benefits, rules_json, popularity, module_category, state) VALUES
(
    'Pradhan Mantri Matru Vandana Yojana (PMMVY)', 
    'A maternity benefit program aimed directly at providing partial compensation for the wage loss in terms of cash incentives so that women can take adequate rest before and after delivery.', 
    'Ministry of Women and Child Development', 
    'Direct cash transfer of ₹5,000 distributed across three installments to support early childbirth and nutrition.', 
    '{"gender": "Female", "age_min": 19}', 
    92, 
    'Women',
    'All India'
),
(
    'Sukanya Samriddhi Yojana (SSY)', 
    'A government-backed savings scheme targeted at the parents of girl children, encouraging them to build a highly lucrative fund for the future education and marriage expenses for their female child.', 
    'Ministry of Finance', 
    'Highly favorable compounding interest rate of 8.2% on deposits with extensive tax exemption benefits under Section 80C.', 
    '{"gender": "Female", "age_max": 21}', 
    97, 
    'Women',
    'All India'
),
(
    'Mahila Shakti Kendra Scheme', 
    'Designed to empower rural women through community participation by creating an environment in which they realize their full potential.', 
    'Ministry of Women and Child Development', 
    'Comprehensive skill development, localized community support programs, employment and integration services.', 
    '{"gender": "Female"}', 
    80, 
    'Women',
    'All India'
),
(
    'Ujjawala Scheme for Women', 
    'A comprehensive dedicated scheme for the prevention of trafficking and for rescue, rehabilitation, and re-integration of female victims into society.', 
    'Ministry of Women and Child Development', 
    'Immediate safety measures, free vocational training, completely free legal guidance, and temporary housing stipends.', 
    '{"gender": "Female"}', 
    89, 
    'Women',
    'All India'
);
