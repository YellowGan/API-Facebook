<?php 

    class Stat 
    {
        private $dbHost     = "localhost";
        private $dbUsername = "db_name";
        private $dbPassword = "db_password";
        private $dbName     = "facebookapi";
        
        function __construct()
        {
            if(!isset($this->db))
            {
                // Connect to the database
                $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
                if($conn->connect_error)
                    die("Failed to connect with MySQL: " . $conn->connect_error);
                else
                    $this->db = $conn;
            }
        }

        function ageRange()
        {
            $queryCheckUser = "SELECT `age_range`, COUNT(`age_range`) AS `n_age_range`  FROM `users`";
            
            $select = $this->db->query($queryCheckUser); 
            $result = [];
            
            for ($i=0; $i < $select->num_rows; $i++)
                array_push($result, $select->fetch_assoc());
            
            return $result;
        }

        function friend()
        {
            $queryCheckUser = "SELECT AVG(`friends`) FROM `users`";

            $select = $this->db->query($queryCheckUser); 

            $result = [];
            
            for ($i=0; $i < $select->num_rows; $i++)
                array_push($result, $select->fetch_assoc());
             
            return $result;
        }

        function os()
        {
            $querySelectOS = "SELECT COUNT(`idDevice`) as `count`, `devices`.`os` FROM `user_device` JOIN `devices` ON (`devices`.`id` = `user_device`.`idDevice`) GROUP BY `devices`.`os`";

            $select = $this->db->query($querySelectOS); 
            $result = [];

            for ($i=0; $i < $select->num_rows; $i++)
                array_push($result, $select->fetch_assoc());
            
            return $result;
        }

        function averagePages()
        {
            $queryNPages = "SELECT COUNT(`id`) as `n_pages` FROM `pages`";
            $select = $this->db->query($queryNPages); 

            $resultP = [];

            for ($i=0; $i < $select->num_rows; $i++)
                array_push($resultP, $select->fetch_assoc());

            $queryNUsers = "SELECT COUNT(`id`) as `n_users` FROM `users`";
            $select = $this->db->query($queryNUsers); 

            $resultU = [];
            $tot = 0;

            for ($i=0; $i < $select->num_rows; $i++)
                array_push($resultU, $select->fetch_assoc());

            foreach ($resultP as $key => $value) 
            {
                foreach ($value as $key1 => $value1) 
                {
                    if ($key1 == "n_pages")
                        $tot = $value1;
                }
            }


            foreach ($resultU as $key => $value) 
            {
                foreach ($value as $key1 => $value1) 
                {
                    if ($key1 == "n_users")
                        $tot /= $value1;
                }
            }

            return $tot;
        }

        function countLocale()
        {
            $queryCountLocale = "SELECT `locale`, COUNT(`locale`) AS `n_locale` FROM `users` GROUP BY `locale`";
            $select = $this->db->query($queryCountLocale); 
            $resultLocale = [];

            for ($i=0; $i < $select->num_rows; $i++)
                array_push($resultLocale, $select->fetch_assoc());

            return $resultLocale;
        }

        function countGender()
        {
            $queryCountGender = "SELECT `gender`, COUNT(`gender`) AS `n_gender` FROM `users` GROUP BY `gender`";
            $select = $this->db->query($queryCountGender); 
            $resultGender = [];

            for ($i=0; $i < $select->num_rows; $i++)
                array_push($resultGender, $select->fetch_assoc());

            return $resultGender;
        }

        function createPieChart($y, $label, $count, $title, $slug)
        {
            echo '

                <script>
                window.onload = function() {

                var chart1 = new CanvasJS.Chart("'.$slug.'", {
                    animationEnabled: true,
                    title: {
                        text:"'.$title.'"
                    },
                    data: [{
                        type: "pie",
                        startAngle: 240,
                        yValueFormatString: "##0.00\"%\"",
                        indexLabel: "{label} {y}",
                        dataPoints: [';

            for ($i=0; $i < $count; $i++)  
                echo '{y: '.$y[$i].', label: "'.$label[$i].'"},';
                        

            echo ']
                    }]
                });
                chart1.render();

                }
                </script>

                <div id="'.$slug.'" style="height: 300px; width: 100%;"></div>';
        }

        function createDoughnutChart($y, $label, $count, $title, $slug)
        {
            echo '

                <script>
                window.onload = function() {

                var chart = new CanvasJS.Chart("'.$slug.'", {
                    animationEnabled: true,
                    title: {
                        text:"'.$title.'"
                    },
                    data: [{
                        type: "doughnut",
                        startAngle: 240,
                        yValueFormatString: "##0.00\"%\"",
                        indexLabel: "{label} {y}",
                        dataPoints: [';

            for ($i=0; $i < $count; $i++)  
                echo '{y: '.$y[$i].', label: "'.$label[$i].'"},';
                        

            echo ']
                    }]
                });
                chart.render();

                }
                </script>

                <div id="'.$slug.'" style="height: 370px; width: 100%;"></div>';
        }

        function percent($data, $tot)
        {
            return $data / $tot * 100;
        }  

    }

?>