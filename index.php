<?php
include('../login_redirect.php');

// Enable PHP debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load environment variables
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/../../config/config.php';
$dotenv = Dotenv\Dotenv::createImmutable(getEnvFilePath());
$dotenv->load();
// db creds
$dbHost = $_ENV['DBHOST'];
$dbUser = $_ENV['DBUSER'];
$dbPass = $_ENV['DBPASS'];
$dbName = $_ENV['DBNAME'];


// Establish a database connection
$connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

// Check if the connection was successful
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Generate a short code
function generateShortCode($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';

    for ($i = 0; $i < $length; $i++) {
        $randomIndex = rand(0, strlen($characters) - 1);
        $code .= $characters[$randomIndex];
    }

    return $code;
}

// Process the form submission or URL creation request
if (isset($_POST['url'])) {
    $originalUrl = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);

    // Check if the URL is valid
    if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
        // Handle invalid URL error
        // Display an error message
        $errorMessage = "Invalid URL.";
    } else {
        // Generate a unique short code
        $shortCode = generateShortCode();

        // Store the original URL and short code in the database
        $query = "INSERT INTO short_urls (original_url, short_code) VALUES ('$originalUrl', '$shortCode')";
        $result = mysqli_query($connection, $query);

        if ($result) {
            // URL stored successfully
            $shortUrl = 'https://toolbox.lepetitpaco.com/tiny/' . $shortCode;
        } else {
            // Error handling
            echo "Error creating short URL.";
        }
    }
}

if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
    $shortCode = explode('/', $_SERVER['REQUEST_URI'])[2];
    $query = "SELECT original_url FROM short_urls WHERE short_code = '$shortCode'";
    $result = mysqli_query($connection, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $originalUrl = $row['original_url'];
        header('Location: ' . $originalUrl);
        exit();
    }
}
// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create Short URL</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #333;
        }

        form {
            width: 50%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            background-color: #222;
        }

        label {
            font-weight: bold;
            color: #fff;
        }

        input[type="text"] {
            width: 100%;
            margin-bottom: 10px;
            background-color: #444;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0069d9;
        }



        .toast-bottom {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
        }

        .toast-body {
            background-color: #28a745;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
        }




        .show {
            display: block !important;
            transition: opacity 0.3s ease-in-out;
            opacity: 1;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <h1 class="text-center mb-4 text-light">Generate Tiny URL</h1>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="post" action="" style="width: 100%;"> <!-- Added style to set width to 100% -->
                    <div class="form-group">
                        <label for="url" class="text-light">URL:</label>
                        <input type="text" name="url" id="url" class="form-control" required>
                    </div>
                    <?php if (isset($errorMessage)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $errorMessage; ?>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary btn-block">Create Short URL</button>
                </form>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        Short URL
                    </div>
                    <div class="card-body">
                        <?php if (isset($shortUrl)): ?>
                            <div class="alert alert-success" role="alert">
                                <a href="<?php echo $shortUrl; ?>"><?php echo $shortUrl; ?></a>
                            </div>
                            <button onclick="copyToClipboard('<?php echo $shortUrl; ?>')" class="btn btn-primary ml-2">Copy
                                to Clipboard</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>




</body>

<script>
    function copyToClipboard(text) {
        var input = document.createElement('textarea');
        input.innerHTML = text;
        document.body.appendChild(input);
        input.select();
        var result = document.execCommand('copy');
        document.body.removeChild(input);
        if (result) {
            var toast = document.createElement('div');
            toast.classList.add('toast');
            toast.classList.add('show');
            toast.classList.add('toast-bottom'); // Add the 'toast-bottom' class to position the toast at the bottom of the page
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.innerHTML = '<div class="toast-body">Copied to clipboard!</div>';
            toast.style.position = 'fixed'; // Set the position of the toast to fixed
            toast.style.bottom = '0'; // Set the bottom property of the toast to 0
            toast.style.left = '50%'; // Set the left property of the toast to 50%
            toast.style.transform = 'translateX(-50%)'; // Center the toast horizontally
            document.body.appendChild(toast);
            setTimeout(function () {
                toast.classList.remove('show');
                document.body.removeChild(toast);
            }, 1000);
        }
    }
</script>



</html>