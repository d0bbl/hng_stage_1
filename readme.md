### PHP API Documentation: Number Properties & Fun Facts

This API retrieves mathematical properties of a number and a fun fact from **NumbersAPI**. The response is in JSON format.

---

## Endpoint

`GET https://hng-stage-1-flame.vercel.app/api/classify-number?number=371`

- **URL Parameter**:  
  `number` (required)  
  Example: `/api/classify-number?number=371`

---

## Response Structure (Success)

```json
{
  "number": 371,
  "is_prime": false,
  "is_perfect": false,
  "properties": ["Armstrong", "odd"],
  "digit_sum": 11,
  "fun_fact": "371 is an Armstrong number"
}
```

| Field           | Description                                                                 |
|-----------------|-----------------------------------------------------------------------------|
| `number`        | The input number.                                                           |
| `is_prime`  | `true` if the number is prime, `false` otherwise.                           |
| `is_perfect`| `true` if the number is a perfect number, `false` otherwise.  
| `properties`    | Array of properties like `"Armstrong"`, `"even"`, `"odd"`, etc.           
| `digit_sum`     | Sum of the digits of the number (e.g., `3 + 7 + 1 = 11`).                   |
| `fun_fact`      | Fun fact from [NumbersAPI](http://numbersapi.com).                          |

---

## Error Handling

**Invalid Request** (e.g., non-numeric input):  
```json
{
  "number": "400",
  "error": true
}
```

---

## Example Usage

### Request
```bash
curl "https://hng-stage-1-flame.vercel.app/api/classify-number?number=371"
```

### Response
```json
{
  "number": 371,
  "is_prime": false,
  "is_perfect": false,
  "properties": ["Armstrong", "odd"],
  "digit_sum": 11,
  "fun_fact": "371 is an Armstrong number."
}
```

---

## PHP Implementation Code

```php
<?php 

// Send headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');


// Validate '$num' as a number
if (isset($_GET['number']) && is_numeric($_GET['number']) && !is_nan((int)$_GET['number'])) {
    $raw_num = intval($_GET['number']);
} elseif (!isset($_GET['number'])){
  die(json_encode(["number" => http_response_code(400),
         "error" => true])); // Handle invalid input
} else {
         http_response_code(400);
         die(json_encode(["number" => $_GET['number'],
         "error" => true])); // Handle invalid input
     }
     

//edge cases
// Handle negative numbers and decimals first
if ($raw_num < 0) {
    $abs_num = abs($raw_num);
    $num_str = (string)$abs_num;  // Work with absolute value for processing
} else {
    $num_str = (string)$raw_num;
}

// Check for decimal point (works for both positive and negative numbers)
if (str_contains($num_str, '.')) {
    $raw_num = (int)round($raw_num);  // Round before processing digits
    $num_str = (string)abs($raw_num); // Update string with rounded absolute value
}

// Convert to array of integers (now safe from '-' and '.')
$num_array = array_map('intval', str_split($num_str));
$count = count($num_array);


// Define the URL and parameters
$url = "http://numbersapi.com/$raw_num/math?json";

$response = file_get_contents($url);

if ($response === false) {
    die (json_encode(["number" =>    http_response_code(500),
         "error" => true]));
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
  return ($num == $sum_total) ? "armstrong": "";
}

function sumDigits($num_array) {
  $sum = array_sum($num_array);
  return $sum;
}

function checkPolarity($num) {
  return ($num % 2 == 0) ? "even" : "odd" ;
}

$response = [
        "number" => $raw_num,
        "is_prime" => isPrime($raw_num),
        "is_perfect" => isPerfect($raw_num),
        "properties" => [...array_values(array_filter([checkArmstrong($raw_num, $sum_total),
          checkPolarity($raw_num)]))
        ],
        "digit_sum" => sumDigits($num_array),
        "fun_fact" => $response_array['text']
    ];
   
// Output JSON
echo json_encode($response);

?>

```

---

---

## Project Structure

```plaintext
your-php-project/
├── api/
│   └── classify-number/
│       └── index.php      # PHP logic for the endpoint
└── vercel.json            # Vercel deployment settings
```

---

## Notes

1. **Input Validation**:  
   - Only accepts numbers. Decimals are rounded and negative numbers are adjusted to positive while non-numeric values trigger an error.
2. **External API Dependency**:  
   - Uses [NumbersAPI](http://numbersapi.com) for fun facts. Ensure your server allows outgoing HTTP requests.
3. **Performance**:  
   - Prime and perfect number checks may slow down for very large numbers (e.g., >10^6).