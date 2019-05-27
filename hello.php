<?php 
require ('vendor/autoload.php');
use Jaxon\Jaxon;
use Jaxon\Response\Response;

// The HelloWorld class
class HelloWorld
{
    public function sayHello($isCaps)
    {
        $text = ($isCaps) ? 'HELLO WORLD!' : 'Hello World!';
        $response = new Response();
        $response->alert($text);
        return $response;
    }
}

// Get the core singleton object
$jaxon = jaxon();

// Register an instance of the class with Jaxon
$jaxon->register(Jaxon::CALLABLE_OBJECT, new HelloWorld());

// Call the Jaxon processing engine
$jaxon->processRequest();

?>
<!doctype html>
<html>
<head>
    <title>Jaxon Simple Test</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/favicon.ico">
<?php
// Insert the Jaxon CSS code into the page
echo $jaxon->getCss();
?>    
</head>
<body>
    <input type="button" value="Submit" onclick="JaxonHelloWorld.sayHello(0);return false;" />
</body>
<?php
// Insert the Jaxon javascript code into the page
echo $jaxon->getJs();
echo $jaxon->getScript();
