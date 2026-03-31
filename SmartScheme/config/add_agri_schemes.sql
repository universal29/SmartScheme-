INSERT INTO schemes (name, ministry, description, benefits, required_docs, application_steps, rules_json, popularity, launch_date, benefit_value, module_category) VALUES
(
    'Pradhan Mantri Fasal Bima Yojana (PMFBY)', 
    'Ministry of Agriculture & Farmers Welfare', 
    'A comprehensive crop insurance scheme to provide financial support to farmers suffering crop loss/damage arising out of unforeseen events. It aims to stabilize the income of farmers to ensure their continuance in farming.', 
    'Comprehensive insurance cover against crop failure', 
    'Aadhar Card, Land Registration Papers, Bank Passbook, Sowing Certificate', 
    'Step 1: Visit PMFBY portal\nStep 2: Register as a farmer\nStep 3: Enter land details and upload documents\nStep 4: Pay the nominal premium\nStep 5: Submit application.', 
    '{"is_farmer": true}', 
    8500, 
    '2016-02-18', 
    100000, 
    'Agriculture'
),
(
    'Kisan Credit Card (KCC) Scheme', 
    'Ministry of Finance', 
    'The scheme aims to provide adequate and timely credit support from the banking system via a single window to the farmers for their short-term credit needs during cultivation and other needs.', 
    'Credit limit up to Rs. 3 Lakh at 4% interest subvention', 
    'Aadhar Card, PAN Card, Land Ownership Records, Passport Size Photo', 
    'Step 1: Visit nearest commercial bank\nStep 2: Fill out KCC application form\nStep 3: Submit land holding details and KYC\nStep 4: Card is issued upon validation.', 
    '{"is_farmer": true}', 
    12400, 
    '1998-08-01', 
    300000, 
    'Agriculture'
),
(
    'Pradhan Mantri Krishi Sinchayee Yojana (PMKSY)', 
    'Ministry of Agriculture', 
    'A national mission to improve farm productivity and ensure better utilization of the resources in the country. Focus is on extending the coverage of irrigation and improving water use efficiency (More crop per drop).', 
    'Subsidy up to 55% on micro-irrigation equipment', 
    'Aadhar, Caste Certificate, Land Ownership Documents (Khatauni), Bank Details', 
    'Step 1: Contact district agriculture office\nStep 2: Submit land records\nStep 3: Wait for surveyor inspection\nStep 4: Apply for subsidized drip/sprinkler systems.', 
    '{"is_farmer": true}', 
    4500, 
    '2015-07-01', 
    50000, 
    'Agriculture'
),
(
    'National Agriculture Market (e-NAM)',
    'Ministry of Agriculture & Farmers Welfare',
    'A pan-India electronic trading portal which networks the existing APMC mandis to create a unified national market for agricultural commodities. It ensures better price discovery for farmers.',
    'Free online registration to sell produce pan-India',
    'Farmer Registration Details, Bank Passbook front page copy, Mobile Number',
    'Step 1: Register on e-NAM portal or mobile app\nStep 2: Enter crop details\nStep 3: Take produce to e-NAM connected mandi\nStep 4: Receive e-Payment directly to bank account.',
    '{"is_farmer": true}',
    6200,
    '2016-04-14',
    0,
    'Agriculture'
);
