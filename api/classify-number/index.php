<?php 

// Send headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

/* Check and sanitize 'name'
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Guest'; */

// Validate '$num' as a number
if (isset($_GET['number']) && is_numeric($_GET['number'])) {
    $num = intval($_GET['number']);
} else {
         die(json_encode(["number" => http_response_code(),
         "error" => "true"])); // Handle invalid input
     }
     

// Step 1: Convert the number to a string
$num_str = (string)$num;

// Step 2: Convert to array
$num_array = array_map('intval', str_split($num_str));

//echo json_encode($num_array);

// Step 3: Count number of digits
$count = strlen($num_str);

//termux check
//echo $count;

//edge case helper
function includes($num, $num_str,$chars) {
    //$numberStr = (string)$num;
    foreach ($chars as $char) {
        if (str_contains($num_str, $char) !== false) {
            return true;
        }
    }
    return false;
}

//edge cases
$decimal = ['.'];
$minus = ['-'];

//check for int
if (!includes($num, $num_str, $minus) && !includes($num, $num_str, $decimal)) {
  
} elseif (includes($num, $num_str, $minus) && includes($num, $num_str, $decimal)) {
  $num = intval((string)abs($num));
  $num = (int)round($num);
} elseif (includes($num, $num_str, $minus)) {
// Remove the negative sign
$num = intval((string)abs($num));
} elseif (includes($num, $num_str,  $decimal)) {
  $num = (int)round($num);
}

// Define the URL and parameters
$url = "http://numbersapi.com/$num/math";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

$numbers_api_response = curl_exec($ch);

// Check for errors
if ($numbers_api_response === false) {
    //termux output
    $error = curl_error($ch);
    die("cURL Error: " . $error);
}

// Get HTTP status code
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($httpCode !== 200) {
  //termux check
   // echo("httpcode not 200");
    die("HTTP Error: Status Code " . $httpCode);
}

// Close the session
curl_close($ch);

/* Process the response (e.g., JSON decode)
$data = json_decode($response, true);
print_r($data); */


//check for prime number
function isPrime($num) {
    if ($num < 2) {
        return false;
    }
    if ($num == 2) {
        return true;
    }
    if ($num % 2 == 0) {
        return false;
    }
    $sqrt_num = sqrt($num);
    for ($i = 3; $i <= $sqrt_num; $i += 2) {
        if ($num % $i == 0) {
            return false;
        }
    }
    return true;
}

//check for perfect number
function isPerfect($num) {
    if ($num < 2) {
        return false;
    }
    $sum = 1; // Start with 1 (proper divisor for numbers > 1)
    $sqrt_num = sqrt($num);
    for ($i = 2; $i <= $sqrt_num; $i++) {
        if ($num % $i == 0) {
            $sum += $i;
            $otherDivisor = $num / $i;
            // Avoid adding duplicate divisors (e.g., for perfect squares)
            if ($otherDivisor != $i) {
                $sum += $otherDivisor;
            }
        }
    }
    return $sum == $num;
}


function addArmstrongNum($num_array, $count) {
  $total_sum = 0;

  for ($i = 0; $i < $count; $i++) {
    $init_sum = $num_array[$i] ** $count;
     $total_sum += $init_sum; 
  }
  //termux check
  //echo $total_sum;
  return $total_sum;
}

$sum_total = addArmstrongNum($num_array, $count);

function checkArmstrong($num, $sum_total) {
  return ($num == $sum_total) ? "Armstrong": "";
}

function sumDigits($num_array) {
  $sum = array_sum($num_array);
  return $sum;
}

function checkPolarity($num) {
  return ($num % 2 == 0) ? "even" : "odd" ;
}

$response = [
        "prime_number" => isPrime($num),
        "perfect_number" => isPerfect($num),
        "digit_sum" => sumDigits($num_array),
        "properties" => [ checkArmstrong($num, $sum_total),
          checkPolarity($num)
        ],
        "digit_sum" => sumDigits($num_array),
        "fun_fact" => $numbers_api_response
    ];
    
// Output JSON
echo json_encode($response);

?>