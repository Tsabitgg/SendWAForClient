<?php

class JWT
{
    public static function decode($jwt, $key = null, $verify = true)
    {
        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            throw new UnexpectedValueException('Wrong number of segments');
        }
        list($headb64, $bodyb64, $cryptob64) = $tks;
        if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))) {
            throw new UnexpectedValueException('Invalid segment encoding');
        }
        if (null === $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64))) {
            throw new UnexpectedValueException('Invalid segment encoding');
        }
        $sig = JWT::urlsafeB64Decode($cryptob64);
        if ($verify) {
            if (empty($header->alg)) {
                throw new DomainException('Empty algorithm');
            }
            if ($sig != JWT::sign("$headb64.$bodyb64", $key, $header->alg)) {
                throw new UnexpectedValueException('Signature verification failed');
            }
        }
        return $payload;
    }

    public static function encode($payload, $key, $algo = 'HS256')
    {
        $header = array('typ' => 'JWT', 'alg' => $algo);
        $segments = array();
        $segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($header));
        $segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($payload));
        $signing_input = implode('.', $segments);
        $signature = JWT::sign($signing_input, $key, $algo);
        $segments[] = JWT::urlsafeB64Encode($signature);
        return implode('.', $segments);
    }

    public static function sign($msg, $key, $method = 'HS256')
    {
        $methods = array(
            'HS256' => 'sha256',
            'HS384' => 'sha384',
            'HS512' => 'sha512',
        );
        if (empty($methods[$method])) {
            throw new DomainException('Algorithm not supported');
        }
        return hash_hmac($methods[$method], $msg, $key, true);
    }

    public static function jsonDecode($input)
    {
        $obj = json_decode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            JWT::_handleJsonError($errno);
        } else if ($obj === null && $input !== 'null') {
            throw new DomainException('Null result with non-null input');
        }
        return $obj;
    }

    public static function jsonEncode($input)
    {
        $json = json_encode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            JWT::_handleJsonError($errno);
        } else if ($json === 'null' && $input !== null) {
            throw new DomainException('Null result with non-null input');
        }
        return $json;
    }

    public static function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    private static function _handleJsonError($errno)
    {
        $messages = array(
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
        );
        throw new DomainException(
            isset($messages[$errno])
                ? $messages[$errno]
                : 'Unknown JSON error: ' . $errno
        );
    }
}

$secretKey = 'TokenJWT_BMI_ICT';

// Array pesan yang akan dikirimkan
$messages = [
    "Terima kasih, {NMCUST}, atas pembayaran sebesar Rp{nominal}. Semoga hari Anda menyenangkan!",
    "Halo, {NMCUST}! Kami telah menerima pembayaran Anda sebesar Rp{nominal}. Terima kasih!",
    "Hai, {NMCUST}! Pembayaran Rp{nominal} telah kami terima. Terima kasih banyak!",
];

try {
    // Ambil token dari query string
    $token = $_GET['token'] ?? null;

    if (!$token) {
        http_response_code(400);
        echo json_encode(["error" => "Token is required."]);
        exit;
    }

    // Decode token JWT
    $decoded = JWT::decode($token, $secretKey);

    $nova = $decoded->nova ?? null;
    $nominal = $decoded->nominal ?? null;

    if (!$nova || !$nominal) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid token payload."]);
        exit;
    }

    $db = new mysqli('10.99.23.18', 'root', 'Smartpay1ct', 'solo_nurhidayah');

    if ($db->connect_error) {
        throw new Exception("Database connection failed: " . $db->connect_error);
    }

    // Select data berdasarkan nova
    $nova = $db->real_escape_string($nova);
    $result = $db->query("SELECT scctbill.BILLAM, scctcust.NOCUST, scctcust.GENUSContact, scctcust.NMCUST 
    FROM scctcust JOIN scctbill ON scctcust.CUSTID = scctbill.CUSTID WHERE 
    CONCAT(scctbill.BILLAM, scctcust.CUSTID) = '$nova'");

    if ($result->num_rows === 0) {
        echo json_encode(["error" => "Data not found for nova: $nova."]);
        exit;
    }

    while ($row = $result->fetch_assoc()) {
        $GENUSContact = $row['GENUSContact'];
        $NMCUST = $row['NMCUST'];

        // Format pesan dengan NMCUST dan nominal
        $messageTemplate = $messages[array_rand($messages)];
        $message = str_replace(['{NMCUST}', '{nominal}'], [$NMCUST, number_format($nominal, 0, ',', '.')], $messageTemplate);

        // Hit API WhatsApp
        $api_url = 'https://api.watzap.id/v1/send_message';
        $data = [
            "api_key" => "1FOPYD2SA8VPIU4Q",
            "number_key" => "yXLAdQbRzkHdvlDJ",
            "phone_no" => $GENUSContact,
            "message" => $message,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            echo json_encode(["error" => "Failed to send message to $GENUSContact."]);
            exit;
        }

        echo json_encode(["success" => "Message sent to $GENUSContact: $message"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
