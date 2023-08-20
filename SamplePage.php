<?php include "../inc/dbinfo.inc"; ?>
<html>
<head>
  <title>Employee Management</title>
</head>
<body>
<h1>Employee Management</h1>
<?php
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$database = mysqli_select_db($connection, DB_DATABASE);

VerifyEmployeesTable($connection, DB_DATABASE);
VerifySalaryTable($connection, DB_DATABASE);

$employee_name = htmlentities($_POST['NAME']);
$employee_address = htmlentities($_POST['ADDRESS']);

$new_salary_employee = htmlentities($_POST['SALARY_EMPLOYEE']);
$new_salary_amount = intval($_POST['SALARY_AMOUNT']);
$new_salary_paid = isset($_POST['SALARY_PAID']) ? 1 : 0;

if (strlen($employee_name) || strlen($employee_address)) {
    AddEmployee($connection, $employee_name, $employee_address);
}

if (strlen($new_salary_employee) && $new_salary_amount > 0) {
    AddSalary($connection, $new_salary_employee, $new_salary_amount, $new_salary_paid);
}
?>

<!-- Employee Input Form -->
<h2>Add Employee</h2>
<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
    <label for="NAME">Name:</label>
    <input type="text" name="NAME" maxlength="45" size="30" /><br>
    <label for="ADDRESS">Address:</label>
    <input type="text" name="ADDRESS" maxlength="90" size="60" /><br>
    <input type="submit" value="Add Employee" />
</form>

<!-- Salary Input Form -->
<h2>Add Salary</h2>
<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
    <label for="SALARY_EMPLOYEE">Employee:</label>
    <input type="text" name="SALARY_EMPLOYEE" /><br>
    <label for="SALARY_AMOUNT">Amount:</label>
    <input type="number" name="SALARY_AMOUNT" /><br>
    <label for="SALARY_PAID">Paid:</label>
    <input type="checkbox" name="SALARY_PAID" value="1" /><br>
    <input type="submit" value="Add Salary" />
</form>

<!-- Display Employees Table -->
<h2>Employees</h2>
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>NAME</td>
    <td>ADDRESS</td>
  </tr>
  <?php
  $result = mysqli_query($connection, "SELECT * FROM EMPLOYEES");

  while ($query_data = mysqli_fetch_row($result)) {
      echo "<tr>";
      echo "<td>", $query_data[0], "</td>",
      "<td>", $query_data[1], "</td>",
      "<td>", $query_data[2], "</td>";
      echo "</tr>";
  }
  ?>
</table>

<!-- Display Salary Table -->
<h2>Salaries</h2>
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>Employee</td>
    <td>Amount</td>
    <td>Paid</td>
  </tr>
  <?php
  $result = mysqli_query($connection, "SELECT * FROM SALARIES");

  while ($query_data = mysqli_fetch_row($result)) {
      echo "<tr>";
      echo "<td>", $query_data[0], "</td>",
      "<td>", $query_data[1], "</td>",
      "<td>", $query_data[2], "</td>",
      "<td>", $query_data[3] ? "Yes" : "No", "</td>";
      echo "</tr>";
  }
  ?>
</table>

<!-- Clean up. -->
<?php
mysqli_free_result($result);
mysqli_close($connection);
?>

</body>
</html>

<?php

/* Add an employee to the EMPLOYEES table. */
function AddEmployee($connection, $name, $address) {
    $n = mysqli_real_escape_string($connection, $name);
    $a = mysqli_real_escape_string($connection, $address);

    $query = "INSERT INTO EMPLOYEES (NAME, ADDRESS) VALUES ('$n', '$a');";

    if (!mysqli_query($connection, $query)) {
        echo("<p>Error adding employee data.</p>");
    }
}

/* Add a salary record to the SALARIES table. */
function AddSalary($connection, $employee, $amount, $paid) {
    $e = mysqli_real_escape_string($connection, $employee);
    $a = intval($amount);
    $p = $paid ? 1 : 0;

    $query = "INSERT INTO SALARIES (EMPLOYEE, AMOUNT, PAID) VALUES ('$e', $a, $p);";

    if (!mysqli_query($connection, $query)) {
        echo("<p>Error adding salary data.</p>");
    }
}

/* Check whether the EMPLOYEES table exists and, if not, create it. */
function VerifyEmployeesTable($connection, $dbName) {
    if (!TableExists("EMPLOYEES", $connection, $dbName)) {
        $query = "CREATE TABLE EMPLOYEES (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         ADDRESS VARCHAR(90)
       )";

        if (!mysqli_query($connection, $query)) {
            echo("<p>Error creating EMPLOYEES table.</p>");
        }
    }
}

/* Check whether the SALARIES table exists and, if not, create it. */
function VerifySalaryTable($connection, $dbName) {
    if (!TableExists("SALARIES", $connection, $dbName)) {
        $query = "CREATE TABLE SALARIES (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         EMPLOYEE VARCHAR(50),
         AMOUNT INT,
         PAID TINYINT(1)
       )";

        if (!mysqli_query($connection, $query)) {
            echo("<p>Error creating SALARIES table.</p>");
        }
    }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
    $t = mysqli_real_escape_string($connection, $tableName);
    $d = mysqli_real_escape_string($connection, $dbName);

    $checktable = mysqli_query($connection,
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

    if (mysqli_num_rows($checktable) > 0) {
        return true;
    }

    return false;
}
?>
