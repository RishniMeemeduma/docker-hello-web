<?php
// Health check endpoint for AWS ECS and Docker health checks
if ($_SERVER['REQUEST_URI'] === '/health' || $_SERVER['REQUEST_URI'] === '/health/') {
    header('Content-Type: application/json');
    http_response_code(200); // Always return 200 OK
    echo json_encode(['status' => 'healthy', 'timestamp' => time()]);
    exit;
}

function insertData($name, $email, $apiUrl) {
    // Validate inputs
    if (empty($name) || empty($email) || empty($apiUrl)) {
        return json_encode(['error' => 'Name and email are required']);
    }
    
    // URL encode the parameters
    $encodedName = urlencode($name);
    $encodedEmail = urlencode($email);
    
    // Build the API URL
    $apiUrl = "$apiUrl/api/insert/$encodedName/$encodedEmail";
    
    // Make the request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        return json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    }
    
    curl_close($ch);
    return $response;
}

function checkCacheResult($apiUrl, $checkCache) {
    // Validate inputs
    if (empty($apiUrl)) {
        return json_encode(['error' => 'API URL is required']);
    }
    
    // Build the API URL for cache status
    $apiUrl = "$apiUrl/api/cache-status";
    
    // Make the request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        return json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    }
    
    curl_close($ch);
    return $response;
}

// Example usage:
// $result = insertData('John Doe', 'john@example.com');
// echo $result;
?>

<!-- Form that calls the PHP function -->
 <h1> RDS Connection</h1>
<form method="post" action="">
    <input type="text" name="apiurl" id="apiurl" value="internal-tif-dev-eu-west-2-alb-api-1563388884.eu-west-2.elb.amazonaws.com" />
    <input type="text" name="name" id="name" placeholder="Enter your name" required />
    <input type="email" name="email" id="email" placeholder="Enter your email" required />
    <button type="submit" name="submit">Submit</button>
</form>

<h1>Cache</h1>
<form method="post" action="">
    <input type="text" name="apiurl" id="apiurl" value="internal-tif-dev-eu-west-2-alb-api-1563388884.eu-west-2.elb.amazonaws.com" />

    <button type="submit" name="check_cache" value="1">Check Cache Connectivity</button>
</form>
<?php if (!empty($cacheCheckResult)): ?>
    <h3>Cache Connectivity Result:</h3>
    <pre><?php echo $cacheCheckResult; ?></pre>
<?php endif; ?>

<?php
if (isset($_POST['submit'])) {
    if (isset($_POST['check_cache'])) {
        $cacheCheckResult = checkCacheResult($_POST['apiurl'], $_POST['check_cache']);
    } else {
        $result = insertData($_POST['name'], $_POST['email'], $_POST['apiurl']);
        echo '<pre>' . $result . '</pre>';
    }
}

