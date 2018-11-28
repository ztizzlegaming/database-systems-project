<?php
session_start();

function isLoggedIn() {
  return isset($_SESSION['username']);
}

   function head($title)
   {
      echo <<<HEAD
      <!DOCTYPE html>
        <html>
          <head>
            <title>$title</title>
          </head>
            <body>
HEAD;
   }

   function tail()
   {
      echo <<<TAIL
        </body>
       </html>
TAIL;
   }

/* This is for "scaffolding" to inspect variables in loops, etc.
   You might also look up highlight_string and print_r */
   function debug_message($message, $continued=FALSE)
   {
     $html = '<span style="color:orange;">';
     $html .= $message . '</span>';
     if ($continued == FALSE) {
       $html .= '<br />';
     }
     $html .= "\n";
     echo $html;
   }
		   

   function connect_to_psql($db, $verbose=FALSE)
   {
     $host = 'localhost';
     $user = 'jayz'; // YOU WILL HAVE TO EDIT THESE
     $pass = 'uiflowboi';

     $dsn = "pgsql:host=$host;dbname=$db;user=$user;password=$pass";
     $options = [
       PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
       PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
       PDO::ATTR_EMULATE_PREPARES   => false,
     ];
     try {
       if ($verbose) {
	 debug_message('Connecting to PostgreSQL DB `classes`...', TRUE);
       }
       $pdo = new PDO($dsn, $user, $pass, $options);
       if ($verbose) {
         debug_message('Success!');
       }
       return $pdo;
     } catch (\PDOException $e) {
         debug_message('Error: Could not connect to database! Aborting!');
	 debug_message($dsn);
	 debug_message($e);
	 throw new \PDOException($e->getMessage(), (int)$e->getCode());
     }
}
									    

?>