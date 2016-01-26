<?php	
	#Set UTF-8 charset for spanish characters
	header('Content-Type: text/html; charset=utf-8');
	
	#Get the remote ip from the visitor
	$ip = $_SERVER['REMOTE_ADDR'];
	
	#Complete the url with the obtained ip and add the translation
	$url = "http://ip-api.com/json/".$ip."?lang=es";
	
	#With the complete url we obtain the json content
	$page = file_get_contents($url);
	
	#Parsing the content
	$contenido = json_decode($page,true);
	
	#Evaluating the response for showing the photos or the error
	if($contenido["status"]=="fail"){
		#When the city is not obtained			
		if(empty($_GET)){
			
			#An error message and a search field are displayed 
			echo "<table align='center' bgcolor='#F2F2F2' cellpadding='20px'><th colspan='2'><h2>Error</h2>
			<form method='get'>Su ip no es reconocible, introduzca una etiqueta:<br>			
			<input type='text'name='tag'><br><input type='submit' value='Aceptar'>
			</form></th></table>";

		#When we do a search without the city
		}else{
			buscarPorTag($_GET['tag']);
		}
	}else{
		#If the city is obtained and still no search
		if(empty($_GET)){

			$ciudad = $contenido["city"];
			buscarPorTag($ciudad);
		
		#If the city is obtained but there is a search
		}else{
			
			buscarPorTag($_GET['tag']);	
		}
	}

	function buscarPorTag($tag){
		#Using the tag we complete the url		
		$flickr = "https://api.flickr.com/services/feeds/photos_public.gne?tags=".$tag;

		#With the complete url we obtain the xml content
		$xml = file_get_contents($flickr);

		#Create a simple xml element using the xml data obtained
		$entradas = new SimpleXMLElement($xml);

		#The namespace is registered for the xpath search
		$entradas->registerXPathNamespace("feed","http://www.w3.org/2005/Atom");

		#Printing the header
		echo "<h1 align='center'>Últimas 10 fotos con la etiqueta ".$tag."</h1><table>";	

		#Declaring the string for the table creation beginning for the header 
		$tabla = "<th colspan='2'>¿Quieres buscar por otra etiqueta?<br><pre>Para usar varias separar con coma(sin espacios)</pre><form method='get'>			
			<input type='text'name='tag'><br><input type='submit' value='Aceptar'>
			</form><th>";

		#Getting links with xpath expression
                
                $lauthor = $entradas->xpath("//feed:entry/feed:author/feed:name");
		$links = $entradas->xpath("//feed:entry/feed:link[@rel='enclosure']/@href");
		
		#We make a loop for showing the results
		for ($i = 0; $i < 10; $i++) {
                  #we add the xml data to the table creation string, navigating through the document and using the previously created array
	          $tabla = $tabla."<tr><td><a href='".$links[$i]."'><img src=".$links[$i]." width='500px'></a></td><td width='300px'><b>Título</b><br>".$entradas->entry[$i]->title."<br><b>Autor</b></br>".$entradas->entry[$i]->author->name."</td></tr>"; 	
                }

		#Finally we print the complete table	
		echo "<table align='center'bgcolor='#F2F2F2' cellpadding='20px'>".$tabla."</table>";
	}
?>
