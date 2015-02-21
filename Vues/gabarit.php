<!DOCTYPE html > 
<html lang="fr"> 
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="/Ressources/Public/css/foundation.min.css" />
    <link rel="stylesheet" type="text/css" href="/Ressources/Public/css/MyCSS.css" />
    <link rel="stylesheet" type="text/css" href="/Ressources/Public/css/ToDoMixine.css" />
    <title>Cat Clinic - Console de gestion</title>
</head>
<body>
	<section id="wrapper">
	    <header><?php Vue::montrer('standard/entete'); ?></header>
	    <div id="messages" class="small-11 medium-8 large-6 small-centered columns"><?php Vue::montrer('standard/erreurs'); ?></div>
	    <section id="content" class="small-11 medium-8 large-6 small-centered columns">
	    	<?php echo $A_vue['body'] ?>
	    </section>   
	    <footer class="text-center"><?php Vue::montrer('standard/pied'); ?></footer>
	</section>
    
    <script src="/Ressources/Public/js/vendor/jquery.js"></script>
    <script src="/Ressources/Public/js/vendor/fastclick.js"></script>
    <script src="/Ressources/Public/js/vendor/jquery.cookie.js"></script>
    <script src="/Ressources/Public/js/vendor/modernizr.js"></script>
    <script src="/Ressources/Public/js/vendor/placeholder.js"></script>
    <script src="/Ressources/Public/js/foundation.min.js"></script>
    <script src="/Ressources/Public/js/document.function.js"></script>
    <script type="text/javascript" src="/Ressources/Public/js/tinymce/tinymce.min.js"></script>
	<script type="text/javascript">
		tinymce.init({
			language : 'fr_FR',
		    selector: "textarea"
		 });
	</script>
    <script>$(document).foundation();</script>
    
</body>
</html>