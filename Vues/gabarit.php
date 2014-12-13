<!DOCTYPE html > 
<html lang="fr"> 
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="/Ressources/Public/css/main.css" />
    <title>Cat Clinic - Console de gestion</title>
</head>
<body>
    <div id="header"><?php Vue::montrer('standard/entete'); ?></div>
    <div id="body">
       <?php echo $A_vue['body'] ?>
    </div>
    <div id="footer"><?php Vue::montrer('standard/pied'); ?></div>
</body>
</html>