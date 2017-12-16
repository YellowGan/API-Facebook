<?php

    class User 
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
        
        function checkUser($userData = array())
        {
            if(!empty($userData))
            {
                try
                {  
                    //devices                
                    $i = 0;
                    
                    if (count($userData['os']) > count($userData['hardware']))
                    {
                        if ((count($userData['os'])) != 0)
                        {
                            for ($i = 0; $i < count($userData['os']); $i++)
                            {
                                if (empty($userData['hardware'][$i]))
                                    $userData['hardware'][$i] = "null";

                                $queryCheckDevice = "SELECT `id` FROM `devices` WHERE `hardware`=\"".$userData['hardware'][$i]."\" AND `os`=\"".$userData['os'][$i]."\"";
                                $select = $this->db->query($queryCheckDevice);
                                $idDevices[] = $select->fetch_assoc();

                                if ($select->num_rows == 0)
                                {
                                    $queryDevices = "INSERT INTO `devices` (`hardware`,`os`) VALUES (\"".$userData['hardware'][$i]."\",\"".$userData['os'][$i]."\")";
                                    $insert = $this->db->query($queryDevices);
                                    $queryCheckDevice = "SELECT `id` FROM `devices` WHERE `hardware`=\"".$userData['hardware'][$i]."\" AND `os`=\"".$userData['os'][$i]."\"";
                                    $select = $this->db->query($queryCheckDevice);
                                    $idDevices[] = $select->fetch_assoc();
                                } 
                            }
                        }
                    }
                    else
                    {
                        if ((count($userData['hardware'])) != 0)
                        {
                            for ($i = 0; $i < count($userData['hardware']); $i++)
                            {
                                if (empty($userData['os'][$i]))
                                    $userData['os'][$i] = "null";

                                $queryCheckDevice = "SELECT `id` FROM `devices` WHERE `hardware`=\"".$userData['hardware'][$i]."\" AND `os`=\"".$userData['os'][$i]."\"";
                                $select = $this->db->query($queryCheckDevice);
                                $idDevices[] = $select->fetch_assoc();

                                if ($select->num_rows == 0)
                                {
                                    $queryDevices = "INSERT INTO `devices` (`hardware`,`os`) VALUES (\"".$userData['hardware'][$i]."\",\"".$userData['os'][$i]."\")";
                                    $insert = $this->db->query($queryDevices);
                                    $queryCheckDevice = "SELECT `id` FROM `devices` WHERE `hardware`=\"".$userData['hardware'][$i]."\" AND `os`=\"".$userData['os'][$i]."\"";
                                    $select = $this->db->query($queryCheckDevice);
                                    $idDevices[] = $select->fetch_assoc();
                                }  
                            }
                        }
                    }

                    //permission                
                    $i = 0;
                    for ($i = 0; $i < count($userData['permission']); $i++)
                    {
                        $queryCheckPermission = "SELECT `id` FROM `permissions` WHERE `name`=\"".$userData['permission'][$i]."\" AND `state`=\"".$userData['status'][$i]."\"";
                        $select = $this->db->query($queryCheckPermission);
                        $idPermission[] = $select->fetch_assoc();

                        if ($select->num_rows == 0)
                        {
                            $queryPermission = "INSERT INTO `permissions` (`name`,`state`) VALUES (\"".$userData['permission'][$i]."\",\"".$userData['status'][$i]."\")";
                            $insert = $this->db->query($queryPermission);
                            $queryCheckPermission = "SELECT `id` FROM `permissions` WHERE `name`=\"".$userData['permission'][$i]."\" AND `state`=\"".$userData['status'][$i]."\"";
                            $select = $this->db->query($queryCheckPermission);
                            $idPermission[] = $select->fetch_assoc();
                        } 
                    }

                    //pages
                    if ((count($userData['pagesID'])) != 0)
                    {
                        $i = 0;
                        for ($i = 0; $i < count($userData['pagesID']); $i++)
                        {
                            $queryCheckPage = "SELECT `id` FROM `pages` WHERE `idPage`=\"".$userData['pagesID'][$i]."\"";
                            $select = $this->db->query($queryCheckPage);
                            $idPage[] = $select->fetch_assoc();

                            if ($select->num_rows == 0)
                            {
                                $queryPage = "INSERT INTO `pages` (`idPage`,`accessToken`,`name`) VALUES (\"".$userData['pagesID'][$i]."\",\"".$userData['pagesAT'][$i]."\",\"".$userData['pagesNM'][$i]."\")";
                                $insert = $this->db->query($queryPage);
                                $queryCheckPage = "SELECT `id` FROM `pages` WHERE `idPage`=\"".$userData['pagesID'][$i]."\"";
                                $select = $this->db->query($queryCheckPage);
                                $idPage[] = $select->fetch_assoc();
                            }
                            else
                            {
                                $queryModifyPage = "UPDATE `pages` SET `accessToken`=\"".$userData['pagesAT'][$i]."\" WHERE `idPage`=\"".$userData['pagesID'][$i]."\"";
                                $update = $this->db->query($queryModifyPage);
                            }
                        }
                    }

                    //users
                    $queryCheckUser = "SELECT `id` FROM `users` WHERE `oauth_uid`=\"".$userData['oauth_uid']."\"";
                    $select = $this->db->query($queryCheckUser); 
                    $idUser = implode(" ",$select->fetch_assoc());

                    if ($select->num_rows == 0)
                    {
                        $queryUser = "INSERT INTO `users` (`oauth_provider`,`oauth_uid`,`first_name`,`last_name`,`email`,`gender`,`locale`,`picture`,`cover`,`link`,`age_range`,`security_settings`,`third_party_id`,`friends`,`created`,`modified`) VALUES (\"".$userData['oauth_provider']."\",\"".$userData['oauth_uid']."\",\"".$userData['first_name']."\",\"".$userData['last_name']."\",\"".$userData['email']."\",\"".$userData['gender']."\",\"".$userData['locale']."\",\"".$userData['picture']."\",\"".$userData['cover']."\",\"".$userData['link']."\",\"".$userData['age_range']."\",\"".$userData['security_settings']."\",\"".$userData['third_party_id']."\",\"".json_encode($userData['friends'])."\",\"".date("Y-m-d H:i:s")."\",\"".date("Y-m-d H:i:s")."\")";
                        $insert = $this->db->query($queryUser);

                        $queryCheckUser = "SELECT `id` FROM `users` WHERE `oauth_uid`=\"".$userData['oauth_uid']."\"";
                        $select = $this->db->query($queryCheckUser); 
                        $idUser = implode(" ",$select->fetch_assoc());
                    }
                    else
                    {
                        $queryModifyUser = "UPDATE `users` SET `first_name`=\"".$userData['first_name']."\",`last_name`=\"".$userData['last_name']."\",`email`=\"".$userData['email']."\",`gender`=\"".$userData['gender']."\",`locale`=\"".$userData['locale']."\",`picture`=\"".$userData['picture']."\",`cover`=\"".$userData['cover']."\",`link`=\"".$userData['link']."\",`age_range`=\"".$userData['age_range']."\",`security_settings`=\"".$userData['security_settings']."\",`third_party_id`=\"".$userData['third_party_id']."\",`friends`=\"".json_encode($userData['friends'])."\",`modified`=\"".date("Y-m-d H:i:s")."\" WHERE `id`=\"".$idUser."\"";
                        $update = $this->db->query($queryModifyUser);
                    }

                    //all N:N
                    if (((count($userData['os'])) != 0) && ((count($userData['hardware'])) != 0))
                    {
                        for ($i = 0; $i < count($idDevices); $i++)
                        {
                            $id = implode(" ",$idDevices[$i]);

                            $queryCheckUserDevice = "SELECT `idUser`,`idDevice` FROM `user_device` WHERE `idUser`=\"".$idUser."\" AND `idDevice`=\"".$id."\"";
                            $select = $this->db->query($queryCheckUserDevice);

                            if ($select->num_rows == 0)
                            {
                                $queryUserDevice = "INSERT INTO `user_device` (`idUser`,`idDevice`) VALUES (\"".$idUser."\",\"".$id."\")";
                                $insert = $this->db->query($queryUserDevice);
                            }
                        }
                    }

                    for ($i = 0; $i < count($idPermission); $i++)
                    {
                        $id = implode(" ",$idPermission[$i]);

                        $queryCheckUserPermission = "SELECT `idUser`,`idPermission` FROM `user_permission` WHERE `idUser`=\"".$idUser."\" AND `idPermission`=\"".$id."\"";
                        $select = $this->db->query($queryCheckUserPermission);

                        if ($select->num_rows == 0)
                        {
                            $queryUserPermission = "INSERT INTO `user_permission` (`idUser`,`idPermission`) VALUES (\"".$idUser."\",\"".$id."\")";
                            $insert = $this->db->query($queryUserPermission);  
                        }
                    }

                    if ((count($userData['pagesID'])) != 0)
                    {
                        for ($i = 0; $i < count($idPage); $i++)
                        {
                            $id = implode(" ",$idPage[$i]);

                            $queryCheckUserPage = "SELECT `idUser`,`idPage` FROM `user_page` WHERE `idUser`=\"".$idUser."\" AND `idPage`=\"".$id."\"";
                            $select = $this->db->query($queryCheckUserPage);

                            if ($select->num_rows == 0)
                            {
                                $queryUserPage = "INSERT INTO `user_page` (`idUser`,`idPage`) VALUES (\"".$idUser."\",\"".$id."\")";
                                $insert = $this->db->query($queryUserPage);
                            }
                        }
                    }

                }
                catch(mysqli_sql_exception $e)
                {
                    echo $e->getTraceAndString();
                }
            }
            
            //Return user data
            return $userData;
        }
    }

?>