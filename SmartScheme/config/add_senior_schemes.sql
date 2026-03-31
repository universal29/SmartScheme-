USE smart_scheme;

INSERT INTO schemes (name, description, ministry, benefits, rules_json, popularity, module_category, state) VALUES
(
    'Pradhan Mantri Vaya Vandana Yojana (PMVVY)', 
    'A pension scheme exclusively for senior citizens aged 60 years and above which provides an assured return of 8% p.a. payable monthly for 10 years.', 
    'Ministry of Finance', 
    'Guaranteed pension ranging from ₹1,000 to ₹9,250 per month based on the invested principal.', 
    '{"age_min": 60}', 
    93, 
    'Senior Citizen',
    'All India'
),
(
    'Indira Gandhi National Old Age Pension Scheme (IGNOAPS)', 
    'A central assistance scheme providing monthly pensions to the destitute elderly falling under the Below Poverty Line (BPL) criteria.', 
    'Ministry of Rural Development', 
    'Monthly pension of ₹200 for citizens aged 60-79, and ₹500 for citizens aged 80 and above.', 
    '{"age_min": 60, "income_max": 100000}', 
    98, 
    'Senior Citizen',
    'All India'
),
(
    'Rashtriya Vayoshri Yojana (RVY)', 
    'A scheme for providing physical aids and assisted-living devices specifically for senior citizens belonging to the BPL category suffering from age-related disabilities.', 
    'Ministry of Social Justice and Empowerment', 
    'Free distribution of walking sticks, wheelchairs, hearing aids, spectacles, and artificial dentures.', 
    '{"age_min": 60, "income_max": 200000}', 
    85, 
    'Senior Citizen',
    'All India'
),
(
    'Varishtha Pension Bima Yojana (VPBY)', 
    'An income security scheme offering an assured pension based on a guaranteed rate of return of 8% per annum for 10 years administered by LIC.', 
    'Ministry of Finance', 
    'Secured long-term financial backing directly credited to the beneficiary account monthly.', 
    '{"age_min": 60}', 
    81, 
    'Senior Citizen',
    'All India'
);
