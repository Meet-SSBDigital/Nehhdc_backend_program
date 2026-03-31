<?php

class QrUtilityDemo
{
    private static $key = "A9F3K7L2X8VQ1M5N4B6C7D8E2R5T9Y1U";
    private static $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ $%*+-./:";

    public static function generateFromString($inputString)
    {
        // Remove { }
        $inputString = trim($inputString, "{}");

        // Step 1: Compress
        $compressed = gzencode($inputString);

        // Step 2: Encrypt
        $encrypted = self::encrypt($compressed);

        // Step 3: Base45 Encode
        return self::base45Encode($encrypted);
    }

    private static function encrypt($data)
    {
        $key = self::$key;

        $iv = random_bytes(16); // 16 byte IV

        $encrypted = openssl_encrypt(
            $data,
            "AES-256-CBC",
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $iv . $encrypted; // prepend IV
    }

    private static function base45Encode($data)
    {
        $charset = self::$charset;
        $len = strlen($data);
        $result = "";

        for ($i = 0; $i < $len; $i += 2)
        {
            if ($i + 1 < $len)
            {
                $x = (ord($data[$i]) << 8) + ord($data[$i + 1]);

                $result .= $charset[$x % 45];
                $result .= $charset[intdiv($x, 45) % 45];
                $result .= $charset[intdiv($x, 45 * 45)];
            }
            else
            {
                $x = ord($data[$i]);

                $result .= $charset[$x % 45];
                $result .= $charset[intdiv($x, 45)];
            }
        }

        return $result;
    }
}

// 🔹 YOUR STRING
$input = "{91213|26030025|2242272130010|DOLI KUMARI|BINOD MANDAL|R-912130026-24|ARTS|339 |1st Division|1,305,38,,,,38,|1,306,75,,,,75 D,|2,319,46,29,,,75 D,|2,323,49,30,,,79 D,|2,324,42,30,,,72,}";

// 🔹 Generate QR Data
$resultforqr = QrUtilityDemo::generateFromString($input);

// 🔹 Output
echo "<h3>Encoded QR Data:</h3>";
echo "<textarea rows='10' cols='100'>$resultforqr</textarea>";


class QrDecryptor
{
    private static $key = "A9F3K7L2X8VQ1M5N4B6C7D8E2R5T9Y1U";
    private static $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ $%*+-./:";

    // 🔹 MAIN FUNCTION
    public static function decryptToString($base45String)
    {
        // Step 1: Base45 Decode
        $decoded = self::base45Decode($base45String);

        // Step 2: Extract IV (first 16 bytes)
        $iv = substr($decoded, 0, 16);
        $cipher = substr($decoded, 16);

        // Step 3: Decrypt
        $decrypted = openssl_decrypt(
            $cipher,
            "AES-256-CBC",
            self::$key,
            OPENSSL_RAW_DATA,
            $iv
        );

        // Step 4: Decompress (GZip)
        $original = gzdecode($decrypted);

        return $original;
    }

    // 🔹 Base45 Decode
    private static function base45Decode($text)
    {
        $charset = self::$charset;
        $len = strlen($text);
        $output = "";

        for ($i = 0; $i < $len; )
        {
            if ($i + 2 < $len)
            {
                $x = strpos($charset, $text[$i])
                   + strpos($charset, $text[$i+1]) * 45
                   + strpos($charset, $text[$i+2]) * 45 * 45;

                $output .= chr($x >> 8);
                $output .= chr($x & 255);

                $i += 3;
            }
            else
            {
                $x = strpos($charset, $text[$i])
                   + strpos($charset, $text[$i+1]) * 45;

                $output .= chr($x);
                $i += 2;
            }
        }

        return $output;
    }
}

// 🔹 PUT YOUR QR STRING HERE (output from previous code)
$qrData = $resultforqr;

// 🔹 Decrypt
$result = QrDecryptor::decryptToString($qrData);

// 🔹 Show result
echo "<h3>Original Data:</h3>";
echo "<textarea rows='10' cols='100'>$result</textarea>";