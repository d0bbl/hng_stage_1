<?php 

// Send headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');


// Validate '$num' as a number
if (isset($_GET['number']) && is_numeric($_GET['number']) && !is_nan((int)$_GET['number'])) {
    $num = intval($_GET['number']);
} else {
         die(json_encode(["number" => 400,
         "error" => "true"])); // Handle invalid input
     }
     

// Step 1: Convert the number to a string
$num_str = (string)$num;

// Step 2: Convert to array
$num_array = array_map('intval', str_split($num_str));

// Step 3: Count number of digits
$count = strlen($num_str);

//edge case helper
function includes($num, $num_str,$chars) {
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
if (includes($num, $num_str, $decimal)) {
    $num = (int)round($num);
}

if (includes($num, $num_str, $minus)) {
    $num = abs($num);
}

// Define the URL and parameters
$url = "http://numbersapi.com/$num/math?json";

$response = file_get_contents($url);

if ($response === false) {
    die (json_encode(["number" => 500,
         "error" => "true"]));
}

$response_array = json_decode($response, true);


//check for prime number
function isPrime($num) {
    if ($num < 2 || $num % 2 == 0) {
        return false;
    }
    if ($num == 2) {
        return true;
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
        "number" => $num,
        "prime_number" => isPrime($num),
        "perfect_number" => isPerfect($num),
        "digit_sum" => sumDigits($num_array),
        "properties" => [...array_values(array_filter([checkArmstrong($num, $sum_total),
          checkPolarity($num)]))
        ],
        "digit_sum" => sumDigits($num_array),
        "fun_fact" => $response_array['text']
    ];
   
// Output JSON
echo json_encode($response);

?>