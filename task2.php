<?php

    // echo "<a href='test2.php?type=page'>Load Html</a></br></br>";
    echo "<a href='test2.php?type=table'>Create wiki_table </a></br></br></br>";
    echo "<a href='test2.php?type=save'>Save Records From wiki page contents</a></br></br>";

    if(isset($_GET['type']) && $_GET['type'] =='page'){
        loadPage();
    }elseif(isset($_GET['type']) && $_GET['type'] =='table'){
        createTable();
    }elseif(isset($_GET['type']) && $_GET['type'] =='save'){
        saveRecords();
    }


    function loadPage(): array
    {
        $url ="https://www.wikipedia.org/";
        $html = file_get_contents($url);
    
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
    
        $headings = array();
    
        foreach($doc->getElementsByTagName('div') as $tag){
            $parrentClassName = $tag->getAttribute('class');
            if (str_contains($parrentClassName, "other-projects")){
                foreach($doc->getElementsByTagName('span') as $element){
                    $className = $element->getAttribute('class');
                    if (str_contains($className, "other-project-title"))
                    {
                        $headings[] = $element->nodeValue;
                    }
                }
            
                //abstract
                $abstracts = array();
                foreach($doc->getElementsByTagName('span') as $element){
                    $className = $element->getAttribute('class');
                    if (str_contains($className, "other-project-tagline"))
                    {
                        $abstracts[] = $element->nodeValue;
                    }
                }
            
                 //Pictures
                $pictures = array();
                foreach($doc->getElementsByTagName('div') as $element){
                    $className = $element->getAttribute('class');
                    if (strcmp($className, "other-project-icon") != true)
                    {
                        $nodediv = $element->childNodes->item(1);
                        $imgClass = $nodediv->getAttribute('class');
                        // $pictures[]= $nodediv->nodeValue;
                        $pictures[]=$imgClass ;
                    }
                    
                }

                //abstract
                $links = array();
                foreach($doc->getElementsByTagName('a') as $element){
                    $className = $element->getAttribute('class');
                    if (strcmp($className, "other-project-link") != true)
                    {
                        $links[] = $element->getAttribute('href');
                    }
                    
                }
            }
    
        }
        $pagedatas=[];
        for($i=0; $i < sizeof($headings); $i++){
            $pagedatas[]= array('title' => $headings[$i],'url' => $links[$i],'picture' => $pictures[$i],'abstract' => $abstracts[$i]);
        }

       return $pagedatas;
        // print_r($pagedatas);
    }

    //database connection
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

    function createTable(): void{
        $conn = connectionString();
        try{
            $sql = "CREATE TABLE IF NOT EXISTS wiki_sections (
                id INT(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                title VARCHAR(230),
                url VARCHAR(240),
                picture VARCHAR(240) UNIQUE,
                abstract VARCHAR(256) UNIQUE
            )";
            if ($conn->query($sql) === TRUE) {
                echo "Table wiki_sections table created successfully"."</br>";
            } else {
                echo "Error creating table: " . $conn->error;
            }
        }catch(mysqli_sql_exception  $e){
            $conn->close();
            exit;
        }
    }

    function saveRecords(): void
    {
        $conn = connectionString();
        $sql = "INSERT INTO wiki_sections(date_created,title,url,picture,abstract)
        VALUES (?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss',$date_created,$title,$url,$picture,$abstract);


        $pagedatas = loadPage();
        $msg = "Records Save Successfully";
        foreach($pagedatas as $data){
            $date_created = date("Y-m-d h:m:s");
            $title = $data['title'];
            $url = $data['url'];
            $picture = $data['picture'];
            $abstract = $data['abstract'];
            if(!$stmt->execute()){
                $msg =  "Error: " .$stmt . "<br>" . $conn->error;
            }
        }

        echo $msg;
        // print_r($pagedatas);
        $stmt->close();
        $conn->close();
    }
?>
