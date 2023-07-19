## Documentation for project

The `project` file is responsible for generating and handling short URLs. It allows users to input a long URL and generates a unique short code for it. The short URL can then be used to redirect to the original long URL.

### File Structure

The `project` file is located in the `toolbox.lepetitpaco.com/tiny` directory. It includes the following files:

- `README.md`: The current file that you are viewing.

### Dependencies

The `project` file requires the following dependencies:

- `vendor/autoload.php`: This file is responsible for autoloading the required classes and functions.
- `config/config.php`: This file contains the configuration settings for the application.

### Database Connection

The `project` file establishes a connection to the database using the provided environment variables. It uses the `mysqli_connect()` function to connect to the database server.

If the connection is successful, the file proceeds with the URL processing logic. If the connection fails, an error message is displayed and the script terminates.

### URL Processing Logic

The `project` file contains the following functions and logic for processing URLs:

1. `generateShortCode($length)`: This function generates a unique short code of the specified length. It uses a combination of alphanumeric characters to create the code.

2. Form Submission: If the `$_POST['url']` variable is set, the file assumes that a form submission has occurred. It retrieves the original URL from the form input and generates a unique short code using the `generateShortCode()` function. It then inserts the original URL and short code into the database.

3. URL Redirection: If the current file is not `project`, the file assumes that a short URL has been accessed. It extracts the short code from the URL and queries the database to retrieve the original URL associated with the short code. If a matching record is found, the file redirects the user to the original URL using the `header()` function.

### HTML and CSS

The `project` file contains HTML and CSS code for the user interface. It uses the Bootstrap framework for styling and includes the necessary CSS and JavaScript files.

The user interface consists of a form where users can input a long URL and a button to generate a short URL. If a short URL is successfully generated, it is displayed in a card along with a button to copy the short URL to the clipboard.

### JavaScript Function

The `project` file includes a JavaScript function called `copyToClipboard(text)`. This function is responsible for copying the provided text to the clipboard. It creates a temporary textarea element, sets its value to the provided text, selects the text, and executes the copy command. If the copy operation is successful, a toast notification is displayed at the bottom of the page.