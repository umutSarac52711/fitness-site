<?php
// Quick test page to verify progress functionality
require_once 'config.php';

// Test database connection
try {
    $pdo->query("SELECT 1");
    echo "✅ Database connection: OK<br>";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// Test if progress_logs table exists
try {
    $result = $pdo->query("DESCRIBE progress_logs");
    echo "✅ progress_logs table: EXISTS<br>";
    echo "<strong>Table structure:</strong><br>";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
} catch (PDOException $e) {
    echo "❌ progress_logs table error: " . $e->getMessage() . "<br>";
}

// Test sample data count
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM progress_logs");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<br>📊 Total progress logs in database: " . $count . "<br>";
} catch (PDOException $e) {
    echo "❌ Error counting progress logs: " . $e->getMessage() . "<br>";
}

// Test users table
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "👥 Total users in database: " . $count . "<br>";
} catch (PDOException $e) {
    echo "❌ Error counting users: " . $e->getMessage() . "<br>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Progress Feature Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-container { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .bmi-result { background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔧 Progress Logging Feature Test</h1>
    
    <div class="test-container">
        <h2>BMI Calculation Test</h2>
        <p>Enter weight and height to test BMI calculation:</p>
        
        <label>Weight (kg): <input type="number" id="weight" step="0.01" placeholder="70.5" oninput="calculateBMI()"></label><br><br>
        <label>Height (cm): <input type="number" id="height" step="0.01" placeholder="175" oninput="calculateBMI()"></label><br><br>
        
        <div class="bmi-result">
            <strong>BMI: <span id="bmi-result">Enter weight and height above</span></strong>
        </div>
    </div>

    <div class="test-container">
        <h2>🔗 Test Links</h2>
        <p><a href="/pages/auth/login.php">Login Page</a></p>
        <p><a href="/pages/auth/account.php">Account Page (requires login)</a></p>
        <p><a href="/pages/auth/register.php">Register Page</a></p>
        <p><a href="/pages/progress/list.php">Admin Progress List (requires admin login)</a></p>
    </div>

    <script>
        function calculateBMI() {
            const weight = parseFloat(document.getElementById('weight').value);
            const height = parseFloat(document.getElementById('height').value);
            const resultElement = document.getElementById('bmi-result');
            
            if (weight > 0 && height > 0) {
                const heightInMeters = height / 100;
                const bmi = (weight / (heightInMeters * heightInMeters)).toFixed(2);
                resultElement.textContent = bmi;
                resultElement.style.color = '#007bff';
                
                // BMI categories
                let category = '';
                if (bmi < 18.5) category = ' (Underweight)';
                else if (bmi < 25) category = ' (Normal weight)';
                else if (bmi < 30) category = ' (Overweight)';
                else category = ' (Obese)';
                
                resultElement.textContent = bmi + category;
            } else {
                resultElement.textContent = 'Enter weight and height above';
                resultElement.style.color = '#666';
            }
        }
    </script>
</body>
</html>
