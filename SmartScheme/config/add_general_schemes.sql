USE smart_scheme;

INSERT INTO schemes (name, description, ministry, benefits, rules_json, popularity, module_category, state) VALUES
(
    'Pradhan Mantri Awas Yojana (PMAY)', 
    'An initiative by the Government of India in which affordable housing will be provided to the urban and rural poor with a target of building large scale localized housing units.', 
    'Ministry of Housing and Urban Affairs', 
    'Direct highly-subsidized interest rates natively applied on housing loans for first-time home buyers in Economically Weaker Section (EWS) categories.', 
    '{"income_max": 600000}', 
    99, 
    'General',
    'All India'
),
(
    'Pradhan Mantri Jeevan Jyoti Bima Yojana (PMJJBY)', 
    'A one-year life insurance scheme officially renewable from year to year offering comprehensive fast-track coverage for death due to any reason.', 
    'Ministry of Finance', 
    'Life cover of ₹2 lakh at a very nominal premium of ₹436 per annum.', 
    '{"age_min": 18, "age_max": 50}', 
    94, 
    'General',
    'All India'
),
(
    'Atal Pension Yojana (APY)', 
    'A core government pension scheme for citizens of India focused squarely on working sector employees who are not members of any statutory social security scheme natively.', 
    'Ministry of Finance', 
    'Secured variable guaranteed minimum pension ranging from ₹1,000 to ₹5,000 per month starting directly at the age of 60 years.', 
    '{"age_min": 18, "age_max": 40}', 
    91, 
    'General',
    'All India'
),
(
    'Stand-Up India Scheme', 
    'Facilitates comprehensive bank loans for setting up greenfield enterprises. This enterprise may be in manufacturing, services or the trading sector entirely.', 
    'Ministry of Finance', 
    'Enables direct bank loans between ₹10 lakh and ₹1 Crore seamlessly specifically targeted for SC, ST, or Women entrepreneurs.', 
    '{"age_min": 18, "categories": ["SC", "ST"]}', 
    85, 
    'General',
    'All India'
);
