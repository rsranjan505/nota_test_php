<?php

function connectionString() {

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "notatest";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        exit;
    }
    return $conn;
}


final class TableCreator
{
    private function create(): void
    {
        $conn = connectionString();
        $sql = "CREATE TABLE Test (
            id INT NOT NULL AUTO_INCREMENT,
            script_name VARCHAR(25) NOT NULL,
            start_time DATETIME NOT NULL,
            end_time DATETIME NOT NULL,
            result VARCHAR(10) NOT NULL,
            PRIMARY KEY (id)
        )";

         if ($conn->query($sql) === TRUE) {
            echo "Table Test table created successfully"."</br>";
        } else {
            echo "Error creating table: " . $conn->error;
        }
    }

    private function fill(): void
    {
        // Generate random data for the table
        $conn = connectionString();
        $datas = [
            [
                'script_name' => 'script1.php',
                'start_time' => date('Y-m-d H:i:s'),
                'end_time' => date('Y-m-d H:i:s', time() + 60),
                'result' => 'normal',
            ],
            [
                'script_name' => 'script2.php',
                'start_time' => date('Y-m-d H:i:s', time() + 300),
                'end_time' => date('Y-m-d H:i:s', time() + 600),
                'result' => 'success',
            ],
            [
                'script_name' => 'script3.php',
                'start_time' => date('Y-m-d H:i:s', time() + 900),
                'end_time' => date('Y-m-d H:i:s', time() + 1200),
                'result' => 'failed',
            ],
        ];

        // Insert the data into the table

        $columns = implode(", ",array_keys($datas));
        $escaped_values = array_map('mysql_real_escape_string', array_values($datas));

        foreach ($escaped_values as $idx=>$data) $escaped_values[$idx] = "'".$data."'";
        $values  = implode(", ", $escaped_values);
        $query = "INSERT INTO Test ($columns) VALUES ($values)";
        if ($conn->query($query) === TRUE) {
            echo "records created successfully";
        } else {
            echo "Error: " . $query . "<br>" . $conn->error;
        }

        $conn->close();
    }

    public function get(): array
    {   
        $conn = connectionString();
        // Select data from the table where result is 'normal' or 'success'
        $sql = "SELECT * FROM Test WHERE result IN ('normal', 'success')";

        // Execute the SQL statement and return the results
        $result = $conn->query($sql);
        $datas=[];
        if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
                $datas[]= array(
                    'id' =>  $row["id"],
                    'script_name' =>  $row["script_name"],
                    'start_time' =>  $row["start_time"],
                    'end_time' =>  $row["end_time"],
                    'result' =>  $row["result"]
                );
            }
        } else {
            return [];
        }

        $conn->close();

        return $datas;
    }
   
}



    $tableCreator = new TableCreator();

    // Create the table
    $tableCreator->create();

    // Fill the table with random data
    $tableCreator->fill();

    // Get the data from the table where result is 'normal' or 'success'
    $data = $tableCreator->get();

    // Print the data
    foreach ($data as $row) {
        echo $row['script_name'] . ' - ' . $row['result'] . PHP_EOL;
    }
?>
