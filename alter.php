<?php
require 'includes/config.php';
try {
    $pdo->exec("ALTER TABLE schemes ADD COLUMN apply_url VARCHAR(255) DEFAULT 'https://india.gov.in'");
} catch(Exception $e) {}
try {
    $pdo->exec("ALTER TABLE schemes ADD COLUMN required_documents TEXT");
} catch(Exception $e) {}
try {
    $pdo->exec("ALTER TABLE schemes ADD COLUMN ai_eligibility_summary TEXT");
} catch(Exception $e) {}
echo "Done";
