<!DOCTYPE html > 
<html lang="fr"> 
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="/Ressources/Public/css/foundation.min.css" />
    <link rel="stylesheet" type="text/css" href="/Ressources/Public/css/MyCSS.css" />
    <title>Cat Clinic - Console de gestion</title>
</head>
<body>
    <header><?php Vue::montrer('standard/entete'); ?></header>
    <div id="messages"><?php Vue::montrer('standard/erreurs'); ?></div>
    <div>
       <?php echo $A_vue['body'] ?>
    </div>
    <footer id="footer"><?php Vue::montrer('standard/pied'); ?></footer>
    
    <script src="/Ressources/Public/js/vendor/jquery.js"></script>
    <script src="/Ressources/Public/js/foundation.min.js"></script>
    <script>$(document).foundation();</script>
    
</body>
</html>