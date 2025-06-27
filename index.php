<?php
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

// Example usage:
// $result = insertData('John Doe', 'john@example.com');
// echo $result;
?>

<!-- Form that calls the PHP function -->
<form method="post" action="">
    <input type="text" name="apiurl" id="apiurl" value="internal-tif-dev-eu-west-2-alb-api-1563388884.eu-west-2.elb.amazonaws.com" />
    <input type="text" name="name" id="name" placeholder="Enter your name" required />
    <input type="email" name="email" id="email" placeholder="Enter your email" required />
    <button type="submit" name="submit">Submit</button>
</form>

<?php
if (isset($_POST['submit'])) {
    $result = insertData($_POST['name'], $_POST['email'], $_POST['apiurl']);
    echo '<pre>' . $result . '</pre>';
}
?>